<?php

namespace App\Controller;

use App\Repository\HelpSectionRepository;
use App\Entity\HelpSection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends AbstractController
{
	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	#[Route('/help/{slug}', name: 'help', defaults: ['slug' => null])]
	public function index(?string $slug, HelpSectionRepository $helpSectionRepository): Response
	{
		// $this->insert($helpSectionRepository);
		$sections = $helpSectionRepository->findAllOrdered();
		$default = $slug !== null
			? $helpSectionRepository->findBySlug($slug)
			: $helpSectionRepository->findFirst();

		if (empty($default))
			throw $this->createNotFoundException('Help section not found.');

		return $this->render('help/index.html.twig', [
			'sections' => $sections,
			'default' => $default->getContent(),
			'activePosition' => $default->getPosition()
		]);
	}

	public function insert(HelpSectionRepository $helpSectionRepository): void
	{
		$section = $helpSectionRepository->find(9);
		$section->setContent(<<<HTML
			<h2 style="text-align: center;">FAǪs</h2>
			<div class="questions_block_wrap" style="width: 100%; padding: 2rem; border-radius: 10px;">
				<div class="question_block">
					<h3>
						<span class="accordion-title-text">How do I book an appointment?</span>
						<span class="accordion-icon" data-accordion-icon="">
							<svg class="accordion-icon-svg" viewBox="0 0 17 8.85">
								<polyline data-accordion-icon-shape="" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" fill="none" fill-rule="evenodd" points="15 1.13 8.5 7.72 2 1.13">
									<animate data-accordion-animate="expand" attributeName="points" values="15 1.13 8.5 7.72 2 1.13; 15.85 4.42 8.5 4.42 1.15 4.42; 15 7.72 8.5 1.13 2 7.72" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.12, 0, 0.38, 0; 0.2, 1, 0.68, 1"></animate>
									<animate data-accordion-animate="collapse" attributeName="points" values="15 7.72 8.5 1.13 2 7.72; 15.85 4.42 8.5 4.42 1.15 4.42; 15 1.13 8.5 7.72 2 1.13" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.2, 0, 0.68, 0; 0.2, 1, 0.68, 1"></animate>
								</polyline>
							</svg>
						</span>
					</h3>
					<p>
						All appointments can be conveniently booked online through our Pabau booking portal.
						Simply click the “Book Now” button on our website to select your treatment, preferred date and practitioner.<br>
						Once booked, you’ll receive instant confirmation and reminder notifications before your
						appointment.<br>
						If you need help or prefer to book directly, contact us:<br>
						<span style="margin-bottom: 1em; display: flex; align-items: center; gap: 1rem;">
							<img src="/media/icons/email.svg" class="email_icon" width="18px" height="18px">
							<a href="mailto:mkaestheticclinics@gmail.com" style="text-decoration: underline; color: inherit;">mkaestheticclinics@gmail.com</a>
						</span>
						<span style="margin-bottom: 1em; display: flex; align-items: center; gap: 1rem;">
							<img src="/media/icons/res1.svg" class="phone_icon" width="18px" height="18px">
							<a href="tel:01702844959" style="text-decoration: underline; color: inherit;">01702844959</a>
						</span>
					</p>
					<hr>
				</div>
				<div class="question_block">
					<h3>
						<span class="accordion-title-text">Can I manage or change my appointment?</span>
						<span class="accordion-icon" data-accordion-icon="">
						<svg class="accordion-icon-svg" viewBox="0 0 17 8.85">
								<polyline data-accordion-icon-shape="" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" fill="none" fill-rule="evenodd" points="15 1.13 8.5 7.72 2 1.13">
									<animate data-accordion-animate="expand" attributeName="points" values="15 1.13 8.5 7.72 2 1.13; 15.85 4.42 8.5 4.42 1.15 4.42; 15 7.72 8.5 1.13 2 7.72" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.12, 0, 0.38, 0; 0.2, 1, 0.68, 1"></animate>
									<animate data-accordion-animate="collapse" attributeName="points" values="15 7.72 8.5 1.13 2 7.72; 15.85 4.42 8.5 4.42 1.15 4.42; 15 1.13 8.5 7.72 2 1.13" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.2, 0, 0.68, 0; 0.2, 1, 0.68, 1"></animate>
								</polyline>
							</svg>
						</span>
					</h3>
					<p>Yes. You can easily reschedule or cancel your appointment, subject to our cancellation policy. You’ll receive email reminders and can manage your booking via the link provided.</p>
					<hr>
				</div>
				<div class="question_block">
					<h3>
						<span class="accordion-title-text">Do I need to complete forms before my visit?</span>
						<span class="accordion-icon" data-accordion-icon="">
							<svg class="accordion-icon-svg" viewBox="0 0 17 8.85">
								<polyline data-accordion-icon-shape="" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" fill="none" fill-rule="evenodd" points="15 1.13 8.5 7.72 2 1.13">
									<animate data-accordion-animate="expand" attributeName="points" values="15 1.13 8.5 7.72 2 1.13; 15.85 4.42 8.5 4.42 1.15 4.42; 15 7.72 8.5 1.13 2 7.72" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.12, 0, 0.38, 0; 0.2, 1, 0.68, 1"></animate>
									<animate data-accordion-animate="collapse" attributeName="points" values="15 7.72 8.5 1.13 2 7.72; 15.85 4.42 8.5 4.42 1.15 4.42; 15 1.13 8.5 7.72 2 1.13" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.2, 0, 0.68, 0; 0.2, 1, 0.68, 1"></animate>
								</polyline>
							</svg>
						</span>
					</h3>
					<p>Yes. Will send you a secure link to complete your medical and consent forms before your appointment. This ensures we understand your health history and can provide the safest, most effective treatment.</p>
					<hr>
				</div>
				<div class="question_block">
					<h3>
						<span class="accordion-title-text">Will my personal data be secure?</span>
						<span class="accordion-icon" data-accordion-icon="">
							<svg class="accordion-icon-svg" viewBox="0 0 17 8.85">
								<polyline data-accordion-icon-shape="" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" fill="none" fill-rule="evenodd" points="15 1.13 8.5 7.72 2 1.13">
									<animate data-accordion-animate="expand" attributeName="points" values="15 1.13 8.5 7.72 2 1.13; 15.85 4.42 8.5 4.42 1.15 4.42; 15 7.72 8.5 1.13 2 7.72" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.12, 0, 0.38, 0; 0.2, 1, 0.68, 1"></animate>
									<animate data-accordion-animate="collapse" attributeName="points" values="15 7.72 8.5 1.13 2 7.72; 15.85 4.42 8.5 4.42 1.15 4.42; 15 1.13 8.5 7.72 2 1.13" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.2, 0, 0.68, 0; 0.2, 1, 0.68, 1"></animate>
								</polyline>
							</svg>
						</span>
					</h3>
					<p>Absolutely. We securely store your personal and medical information. Your data is fully encrypted and only accessible by authorised clinical staff.</p>
					<hr>
				</div>
				<div class="question_block">
					<h3>
						<span class="accordion-title-text">Do I need a consultation before treatment?</span>
						<span class="accordion-icon" data-accordion-icon="">
							<svg class="accordion-icon-svg" viewBox="0 0 17 8.85">
								<polyline data-accordion-icon-shape="" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" fill="none" fill-rule="evenodd" points="15 1.13 8.5 7.72 2 1.13">
									<animate data-accordion-animate="expand" attributeName="points" values="15 1.13 8.5 7.72 2 1.13; 15.85 4.42 8.5 4.42 1.15 4.42; 15 7.72 8.5 1.13 2 7.72" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.12, 0, 0.38, 0; 0.2, 1, 0.68, 1"></animate>
									<animate data-accordion-animate="collapse" attributeName="points" values="15 7.72 8.5 1.13 2 7.72; 15.85 4.42 8.5 4.42 1.15 4.42; 15 1.13 8.5 7.72 2 1.13" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.2, 0, 0.68, 0; 0.2, 1, 0.68, 1"></animate>
								</polyline>
							</svg>
						</span>
					</h3>
					<p>Yes. Every client begins with a comprehensive consultation, either booked online through our system or arranged personally with our team. This allows us to understand your individual goals, assess your skin and medical history, and recommend the most suitable treatment plan tailored to your needs.</p>
					<hr>
				</div>
				<div class="question_block">
					<h3>
						<span class="accordion-title-text">Will my results look natural?</span>
						<span class="accordion-icon" data-accordion-icon="">
							<svg class="accordion-icon-svg" viewBox="0 0 17 8.85">
								<polyline data-accordion-icon-shape="" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" fill="none" fill-rule="evenodd" points="15 1.13 8.5 7.72 2 1.13">
									<animate data-accordion-animate="expand" attributeName="points" values="15 1.13 8.5 7.72 2 1.13; 15.85 4.42 8.5 4.42 1.15 4.42; 15 7.72 8.5 1.13 2 7.72" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.12, 0, 0.38, 0; 0.2, 1, 0.68, 1"></animate>
									<animate data-accordion-animate="collapse" attributeName="points" values="15 7.72 8.5 1.13 2 7.72; 15.85 4.42 8.5 4.42 1.15 4.42; 15 1.13 8.5 7.72 2 1.13" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.2, 0, 0.68, 0; 0.2, 1, 0.68, 1"></animate>
								</polyline>
							</svg>
						</span>
					</h3>
					<p>Yes — our ethos is “enhance, never change.” We aim for subtle, balanced, and natural-looking results tailored to your unique features.</p>
					<hr>
				</div>
				<div class="question_block">
					<h3>
						<span class="accordion-title-text">Are the treatments safe?</span>
						<span class="accordion-icon" data-accordion-icon="">
							<svg class="accordion-icon-svg" viewBox="0 0 17 8.85">
								<polyline data-accordion-icon-shape="" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" fill="none" fill-rule="evenodd" points="15 1.13 8.5 7.72 2 1.13">
									<animate data-accordion-animate="expand" attributeName="points" values="15 1.13 8.5 7.72 2 1.13; 15.85 4.42 8.5 4.42 1.15 4.42; 15 7.72 8.5 1.13 2 7.72" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.12, 0, 0.38, 0; 0.2, 1, 0.68, 1"></animate>
									<animate data-accordion-animate="collapse" attributeName="points" values="15 7.72 8.5 1.13 2 7.72; 15.85 4.42 8.5 4.42 1.15 4.42; 15 1.13 8.5 7.72 2 1.13" dur="320ms" begin="indefinite" fill="freeze" keyTimes="0; 0.5; 1" calcMode="spline" keySplines="0.2, 0, 0.68, 0; 0.2, 1, 0.68, 1"></animate>
								</polyline>
							</svg>
						</span>
					</h3>
					<p>All procedures are performed by qualified practitioners using medical-grade products and clinically proven technology. Your safety and wellbeing are our top priority.</p>
					<hr>
				</div>
			</div>
		HTML);

		$this->em->persist($section);
		$this->em->flush();

	}

	#[Route('/help/load/{slug}', name: 'help_load', methods: ['GET'])]
	public function load(string $slug, HelpSectionRepository $repo): JsonResponse
	{
		$section = $repo->findBySlug($slug);

		if (!$section) {
			return new JsonResponse(['error' => 'Not found'], 404);
		}

		return new JsonResponse([
			'title' => $section->getTitle(),
			'content' => $section->getContent()
		]);
	}

}
