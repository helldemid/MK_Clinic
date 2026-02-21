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
	#[Route('/admin/site-content', name: 'admin_site_content')]
	public function edit(
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
			->generateUrl();

		return $this->redirect($url);
	}
}
