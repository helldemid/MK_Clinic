<?php

namespace App\Controller\Admin;

use App\Entity\HelpFaq;
use App\Entity\HelpSection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class HelpFaqCrudController extends AbstractCrudController
{
	private const POSITION_OFFSET = 1000000;

	public const PAGE_EMBEDDED_NEW = 'embedded_new';
	public const PAGE_EMBEDDED_EDIT = 'embedded_edit';

	public static function getEntityFqcn(): string
	{
		return HelpFaq::class;
	}

	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular('Help FAQ')
			->setEntityLabelInPlural('Help FAQs')
			->showEntityActionsInlined()
			->setDefaultSort(['position' => 'ASC', 'id' => 'ASC']);
	}

	public function configureFilters(Filters $filters): Filters
	{
		$sectionFilter = EntityFilter::new('section')
			->setFormTypeOption('value_type_options.query_builder', static fn (EntityRepository $entityRepository) => $entityRepository->createQueryBuilder('section')
				->andWhere('section.faqSection = :faqSection')
				->setParameter('faqSection', true)
				->orderBy('section.position', 'ASC')
				->addOrderBy('section.id', 'ASC'));

		return $filters
			->add($sectionFilter)
			->add(TextFilter::new('question'))
			->add(NumericFilter::new('position'));
	}

	public function configureFields(string $pageName): iterable
	{
		$section = AssociationField::new('section')
			->setCrudController(HelpSectionCrudController::class)
			->autocomplete()
			->setQueryBuilder(static function (QueryBuilder $queryBuilder): QueryBuilder {
				$rootAlias = $queryBuilder->getRootAliases()[0] ?? 'entity';

				return $queryBuilder
				->andWhere(sprintf('%s.faqSection = :faqSection', $rootAlias))
				->setParameter('faqSection', true)
				->orderBy(sprintf('%s.position', $rootAlias), 'ASC')
				->addOrderBy(sprintf('%s.id', $rootAlias), 'ASC');
			});
		$question = TextField::new('question');
		$answer = TextareaField::new('answer')
			->setFormType(CKEditorType::class)
			->setFormTypeOptions([
				'config_name' => 'help_section',
				'attr' => ['rows' => 8],
			]);
		$position = IntegerField::new('position');

		if (\in_array($pageName, [self::PAGE_EMBEDDED_NEW, self::PAGE_EMBEDDED_EDIT], true)) {
			return [
				$question,
				$answer,
				$position,
			];
		}

		if (Crud::PAGE_INDEX === $pageName) {
			return [
				IdField::new('id'),
				$section,
				$question,
				$position,
			];
		}

		return [
			$section,
			$question,
			$answer,
			$position,
		];
	}

	public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof HelpFaq) {
			parent::persistEntity($entityManager, $entityInstance);
			return;
		}

		$section = $entityInstance->getSection();
		if ($section === null) {
			parent::persistEntity($entityManager, $entityInstance);
			return;
		}

		$orderedFaqs = $this->buildOrderedFaqs($entityManager, $section, $entityInstance, $entityInstance->getPosition());
		$entityManager->persist($entityInstance);
		$this->applyOrderedPositions($entityManager, $orderedFaqs);
	}

	public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof HelpFaq) {
			parent::updateEntity($entityManager, $entityInstance);
			return;
		}

		$section = $entityInstance->getSection();
		if ($section === null) {
			parent::updateEntity($entityManager, $entityInstance);
			return;
		}

		$originalSection = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance)['section'] ?? null;
		$orderedFaqs = $this->buildOrderedFaqs($entityManager, $section, $entityInstance, $entityInstance->getPosition());
		$this->applyOrderedPositions($entityManager, $orderedFaqs);

		if ($originalSection instanceof HelpSection && $originalSection !== $section) {
			$this->compactSectionFaqs($entityManager, $originalSection);
		}
	}

	public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
	{
		if (!$entityInstance instanceof HelpFaq) {
			parent::deleteEntity($entityManager, $entityInstance);
			return;
		}

		$section = $entityInstance->getSection();
		parent::deleteEntity($entityManager, $entityInstance);

		if ($section instanceof HelpSection) {
			$this->compactSectionFaqs($entityManager, $section);
		}
	}

	/**
	 * @return array<int, HelpFaq>
	 */
	private function buildOrderedFaqs(EntityManagerInterface $entityManager, HelpSection $section, HelpFaq $movedFaq, int $targetPosition): array
	{
		/** @var array<int, HelpFaq> $faqs */
		$faqs = $entityManager->getRepository(HelpFaq::class)->findBy(
			['section' => $section],
			['position' => 'ASC', 'id' => 'ASC'],
		);

		$faqs = array_values(array_filter(
			$faqs,
			static fn (HelpFaq $faq): bool => $faq->getId() !== $movedFaq->getId(),
		));

		$clampedPosition = max(0, min($targetPosition, count($faqs)));
		array_splice($faqs, $clampedPosition, 0, [$movedFaq]);

		return $faqs;
	}

	private function compactSectionFaqs(EntityManagerInterface $entityManager, HelpSection $section): void
	{
		/** @var array<int, HelpFaq> $faqs */
		$faqs = $entityManager->getRepository(HelpFaq::class)->findBy(
			['section' => $section],
			['position' => 'ASC', 'id' => 'ASC'],
		);

		if ($faqs === []) {
			return;
		}

		$this->applyOrderedPositions($entityManager, $faqs);
	}

	/**
	 * @param array<int, HelpFaq> $faqs
	 */
	private function applyOrderedPositions(EntityManagerInterface $entityManager, array $faqs): void
	{
		foreach ($faqs as $index => $faq) {
			$faq->setPosition(self::POSITION_OFFSET + $index);
			$entityManager->persist($faq);
		}
		$entityManager->flush();

		foreach ($faqs as $index => $faq) {
			$faq->setPosition($index);
			$entityManager->persist($faq);
		}
		$entityManager->flush();
	}
}
