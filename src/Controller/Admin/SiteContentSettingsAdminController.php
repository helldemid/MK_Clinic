<?php

namespace App\Controller\Admin;

use App\Entity\SiteContentSettings;
use App\Repository\SiteContentSettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class SiteContentSettingsAdminController extends AbstractController
{
	#[Route('/admin/site-content/promo', name: 'admin_site_content')]
	public function promo(
		SiteContentSettingsRepository $settingsRepository,
		EntityManagerInterface $entityManager,
		AdminUrlGenerator $adminUrlGenerator
	): Response {
		return $this->redirectToSection(
			SiteContentSettingsCrudController::SECTION_PROMO,
			$settingsRepository,
			$entityManager,
			$adminUrlGenerator
		);
	}

	#[Route('/admin/site-content/homepage/hero', name: 'admin_homepage_hero')]
	public function hero(
		SiteContentSettingsRepository $settingsRepository,
		EntityManagerInterface $entityManager,
		AdminUrlGenerator $adminUrlGenerator
	): Response {
		return $this->redirectToSection(
			SiteContentSettingsCrudController::SECTION_HERO,
			$settingsRepository,
			$entityManager,
			$adminUrlGenerator
		);
	}

	#[Route('/admin/site-content/price-list/hero', name: 'admin_price_list_hero')]
	public function priceListHero(
		SiteContentSettingsRepository $settingsRepository,
		EntityManagerInterface $entityManager,
		AdminUrlGenerator $adminUrlGenerator
	): Response {
		return $this->redirectToSection(
			SiteContentSettingsCrudController::SECTION_PRICE_LIST_HERO,
			$settingsRepository,
			$entityManager,
			$adminUrlGenerator
		);
	}

	#[Route('/admin/site-content/contact-us', name: 'admin_contact_content')]
	public function contacts(
		SiteContentSettingsRepository $settingsRepository,
		EntityManagerInterface $entityManager,
		AdminUrlGenerator $adminUrlGenerator
	): Response {
		return $this->redirectToSection(
			SiteContentSettingsCrudController::SECTION_CONTACTS,
			$settingsRepository,
			$entityManager,
			$adminUrlGenerator
		);
	}

	#[Route('/admin/site-content/homepage/our-ethos', name: 'admin_homepage_our_ethos')]
	public function ourEthos(
		SiteContentSettingsRepository $settingsRepository,
		EntityManagerInterface $entityManager,
		AdminUrlGenerator $adminUrlGenerator
	): Response {
		return $this->redirectToSection(
			SiteContentSettingsCrudController::SECTION_OUR_ETHOS,
			$settingsRepository,
			$entityManager,
			$adminUrlGenerator
		);
	}

	#[Route('/admin/site-content/homepage/our-story', name: 'admin_homepage_our_story')]
	public function ourStory(
		SiteContentSettingsRepository $settingsRepository,
		EntityManagerInterface $entityManager,
		AdminUrlGenerator $adminUrlGenerator
	): Response {
		return $this->redirectToSection(
			SiteContentSettingsCrudController::SECTION_OUR_STORY,
			$settingsRepository,
			$entityManager,
			$adminUrlGenerator
		);
	}

	#[Route('/admin/site-content/homepage/consultation', name: 'admin_homepage_consultation')]
	public function consultation(
		SiteContentSettingsRepository $settingsRepository,
		EntityManagerInterface $entityManager,
		AdminUrlGenerator $adminUrlGenerator
	): Response {
		return $this->redirectToSection(
			SiteContentSettingsCrudController::SECTION_CONSULTATION,
			$settingsRepository,
			$entityManager,
			$adminUrlGenerator
		);
	}

	#[Route('/admin/site-content/homepage/booking', name: 'admin_homepage_booking')]
	public function booking(
		SiteContentSettingsRepository $settingsRepository,
		EntityManagerInterface $entityManager,
		AdminUrlGenerator $adminUrlGenerator
	): Response {
		return $this->redirectToSection(
			SiteContentSettingsCrudController::SECTION_BOOKING,
			$settingsRepository,
			$entityManager,
			$adminUrlGenerator
		);
	}

	#[Route('/admin/site-content/footer', name: 'admin_footer_content')]
	public function footer(
		SiteContentSettingsRepository $settingsRepository,
		EntityManagerInterface $entityManager,
		AdminUrlGenerator $adminUrlGenerator
	): Response {
		return $this->redirectToSection(
			SiteContentSettingsCrudController::SECTION_FOOTER,
			$settingsRepository,
			$entityManager,
			$adminUrlGenerator
		);
	}

	private function redirectToSection(
		string $section,
		SiteContentSettingsRepository $settingsRepository,
		EntityManagerInterface $entityManager,
		AdminUrlGenerator $adminUrlGenerator
	): Response {
		$settings = $settingsRepository->findSingleton();
		if ($settings === null) {
			$settings = new SiteContentSettings();
			$entityManager->persist($settings);
			$entityManager->flush();
		}

		$url = $adminUrlGenerator
			->unsetAll()
			->setController(SiteContentSettingsCrudController::class)
			->setAction(Action::EDIT)
			->setEntityId($settings->getId())
			->set('section', $section)
			->generateUrl();

		return $this->redirect($url);
	}
}
