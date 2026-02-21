<?php

namespace App\Controller\Admin;

use App\Entity\HelpSection;
use App\Entity\HelpFaq;
use App\Entity\PriceCell;
use App\Entity\PriceColumn;
use App\Entity\PriceRow;
use App\Entity\PriceSection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
	#[Route('/admin', name: 'admin')]
	public function index(): Response
	{
		$adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

		return $this->redirect(
			$adminUrlGenerator
				->setController(PriceSectionCrudController::class)
				->generateUrl(),
		);
	}

	public function configureDashboard(): Dashboard
	{
		return Dashboard::new()
			->setTitle('Clinic Admin');
	}

	public function configureCrud(): Crud
	{
		return Crud::new()
			->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
			->addFormTheme('admin/form/price_grid_widget.html.twig')
			->addFormTheme('admin/form/promo_rotator_widget.html.twig');
	}

	public function configureMenuItems(): iterable
	{
		yield MenuItem::subMenu('Price Management', 'fa-solid fa-tags')->setSubItems([
			MenuItem::linkToCrud('Price Sections', 'fa-solid fa-layer-group', PriceSection::class),
			MenuItem::linkToCrud('Price Columns', 'fa-solid fa-table-columns', PriceColumn::class),
			MenuItem::linkToCrud('Price Rows', 'fa-solid fa-grip-lines', PriceRow::class),
			MenuItem::linkToCrud('Price Cells', 'fa-solid fa-hashtag', PriceCell::class),
		]);

		yield MenuItem::subMenu('Content', 'fa-solid fa-file-lines')->setSubItems([
			MenuItem::linkToCrud('Help Sections', 'fa-solid fa-circle-question', HelpSection::class),
			MenuItem::linkToCrud('Help FAQs', 'fa-solid fa-list-check', HelpFaq::class),
			MenuItem::linkToRoute('Site Content', 'fa-solid fa-bullhorn', 'admin_site_content'),
		]);
	}
}
