<?php

namespace App\Entity;

use App\Repository\SiteContentSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteContentSettingsRepository::class)]
#[ORM\Table(name: 'site_content_settings')]
class SiteContentSettings
{
	public const DEFAULT_HERO_HEADLINE_LINE_1 = 'SCIENCE. ARTISTRY. YOU.';
	public const DEFAULT_HERO_HEADLINE_LINE_2 = 'M.K. AESTHETIC CLINIC';
	public const DEFAULT_HERO_SUBHEADLINE = 'Experience a new era of aesthetics — restore youth, refine beauty, reveal confidence';
	public const DEFAULT_PRICE_LIST_HERO_EYEBROW = 'Science. Artistry. You.';
	public const DEFAULT_PRICE_LIST_HERO_TITLE = 'Price List';
	public const DEFAULT_PRICE_LIST_HERO_LEAD = 'Discover our comprehensive range of aesthetic treatments, from advanced laser procedures to rejuvenating injectables — all delivered with precision and care.';
	public const DEFAULT_PRICE_LIST_HERO_HIGHLIGHT_1 = 'Course discounts available';
	public const DEFAULT_PRICE_LIST_HERO_HIGHLIGHT_2 = 'Free consultations';
	public const DEFAULT_PRICE_LIST_HERO_HIGHLIGHT_3 = 'Flexible payment options';
	/**
	 * @var array<int, array{title: string, note: string, items: array<int, array{icon: string, label: string, url: string, position: int}>, position: int}>
	 */
	public const DEFAULT_CONTACT_DETAIL_BLOCKS = [
		[
			'title' => 'Our phone number:',
			'note' => "Mon–Fri 10:00–18:00\nSat 10:00–16:00\nSun closed",
			'position' => 0,
			'items' => [
				['icon' => 'res1.svg', 'label' => '+44-1702-844-959', 'url' => 'tel:+441702844959', 'position' => 0],
				['icon' => 'res1.svg', 'label' => '+44-7717-205-281', 'url' => 'tel:+447717205281', 'position' => 1],
			],
		],
		[
			'title' => 'Email:',
			'note' => '',
			'position' => 1,
			'items' => [
				['icon' => 'email.svg', 'label' => 'mkaestheticclinics@gmail.com', 'url' => 'mailto:mkaestheticclinics@gmail.com', 'position' => 0],
				['icon' => 'email.svg', 'label' => 'info@mk-aesthetic-clinic.com', 'url' => 'mailto:info@mk-aesthetic-clinic.com', 'position' => 1],
			],
		],
		[
			'title' => 'Address:',
			'note' => '',
			'position' => 2,
			'items' => [
				['icon' => 'location_grey.svg', 'label' => '135 St Clements, Broadway Leigh-on-Sea SS9 1PJ', 'url' => 'https://maps.app.goo.gl/842Yxuxdt8QUfx6k9', 'position' => 0],
			],
		],
	];

	/**
	 * @var array<int, array{title: string, items: array<int, array{icon: string, label: string, url: string, position: int}>, position: int}>
	 */
	public const DEFAULT_CONTACT_ICON_BLOCKS = [
		[
			'title' => 'We in social networks:',
			'position' => 0,
			'items' => [
				['icon' => 'instagram.svg', 'label' => 'Instagram', 'url' => 'https://www.instagram.com/mkaesthetic.clinic?igsh=c2g4NzRuOHh6MWNi', 'position' => 0],
				['icon' => 'facebook.svg', 'label' => 'Facebook', 'url' => 'https://www.facebook.com/share/1HT2JREY7s/', 'position' => 1],
				['icon' => 'whatsapp.svg', 'label' => 'WhatsApp', 'url' => 'https://wa.me/447717205281', 'position' => 2],
			],
		],
	];

	public const DEFAULT_OUR_ETHOS_TITLE = 'Where Science Meets Artistry';
	public const DEFAULT_OUR_ETHOS_BODY = "Driven by a deep commitment to advancing every aspect of skin health, M.K. Aesthetic Clinic was founded on Broadway in Leigh-on-Sea, where we specialise in aesthetic medicine and anti-ageing treatments.\n\nLed by a team of elite practitioners, the clinic is renowned for its minimalist and integrated approach — combining advanced techniques and complementary modalities to achieve results that are always natural-looking, balanced, and refined.\n\nAt M.K. Aesthetic Clinic, our philosophy is simple: to deliver youthful, rejuvenated radiance through cutting-edge, clinically proven treatments, grounded in science and guided by artistry.\n\nAt the heart of our ethos lies a simple promise: to enhance, never to change — and to reveal your most authentic, naturally beautiful self.";

	public const DEFAULT_OUR_STORY_TITLE = 'Where It All Began';
	public const DEFAULT_OUR_STORY_BODY = "Situated on Broadway in Leigh-on-Sea, M.K. Aesthetic Clinic was founded with a clear vision — to enhance natural beauty and restore confidence through subtle, refined results.\n\nWe specialise in advanced skincare and anti-ageing treatments, blending the latest innovations in aesthetic medicine with a highly personalised approach. Every treatment is thoughtfully designed to harmonise with your unique features, ensuring results that are elegant, balanced, and authentically you.\n\nOur team of experienced and internationally trained practitioners are dedicated to delivering the highest standards of safety and care. Using only clinically proven techniques and premium medical-grade products, we provide outcomes that are both visible and lasting.\n\nFrom bespoke skin treatments to advanced injectable procedures, we are committed to helping you feel confident, refreshed, and naturally radiant.";

	public const DEFAULT_CONSULTATION_TITLE = 'Book your consultation';
	public const DEFAULT_CONSULTATION_BODY = 'Get personalized advice and clear answers to your questions. Reserve your consultation and take the first step toward informed, confident decisions.';
	public const DEFAULT_CONSULTATION_EYEBROW = 'About consultation & expert guidance';
	public const DEFAULT_CONSULTATION_BUTTON_LABEL = 'Get now';

	public const DEFAULT_BOOKING_TITLE = 'Book your visit';
	public const DEFAULT_BOOKING_BODY = 'Reserve a spot and enjoy a seamless experience tailored to your needs. From personal consultations to exclusive sessions — secure your time with us and be sure you won’t miss out.';
	public const DEFAULT_BOOKING_EYEBROW = 'About bookings & what we offer';
	public const DEFAULT_BOOKING_BUTTON_LABEL = 'Book now';

	public const DEFAULT_FOOTER_DESCRIPTION = 'Personalised aesthetic treatments with a focus on safety, comfort and natural results. Every treatment is tailored to you, using advanced techniques to enhance your natural beauty while keeping results subtle, balanced and refined.';

	/**
	 * @var array<int, array{icon: string, url: string, label: string, position: int}>
	 */
	public const DEFAULT_FOOTER_SOCIAL_LINKS = [
		['icon' => 'fa-brands fa-instagram', 'url' => 'https://www.instagram.com/mkaesthetic.clinic?igsh=c2g4NzRuOHh6MWNi', 'label' => 'Instagram', 'position' => 0],
		['icon' => 'fa-brands fa-facebook-f', 'url' => 'https://www.facebook.com/share/1HT2JREY7s/', 'label' => 'Facebook', 'position' => 1],
		['icon' => 'fa-brands fa-whatsapp', 'url' => 'https://wa.me/447717205281', 'label' => 'WhatsApp', 'position' => 2],
	];

	/**
	 * @var array<int, array{sourceKey: string, label: string, position: int}>
	 */
	public const DEFAULT_FOOTER_CUSTOMER_CARE_LINKS = [
		['sourceKey' => 'help:rewards-programme', 'label' => 'Rewards Programme', 'position' => 0],
		['sourceKey' => 'help:cancellation-policy', 'label' => 'Cancellation Policy', 'position' => 1],
		['sourceKey' => 'help:complaints-policy', 'label' => 'Complaints Policy', 'position' => 2],
		['sourceKey' => 'help:chaperone-policy', 'label' => 'Chaperone Policy', 'position' => 3],
		['sourceKey' => 'help:faqs', 'label' => 'FAQs', 'position' => 4],
	];

	/**
	 * @var array<int, array{sourceKey: string, label: string, position: int}>
	 */
	public const DEFAULT_FOOTER_COMPANY_LEGAL_LINKS = [
		['sourceKey' => 'help:access-statement', 'label' => 'Access Statement', 'position' => 0],
		['sourceKey' => 'help:patient-notice', 'label' => 'Patient Notice', 'position' => 1],
		['sourceKey' => 'help:privacy-policy', 'label' => 'Privacy Policy', 'position' => 2],
		['sourceKey' => 'route:contacts', 'label' => 'Contact Us', 'position' => 3],
		['sourceKey' => 'help:career', 'label' => 'Career', 'position' => 4],
	];

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	/**
	 * @var array<int, array{text: string, url: string}>
	 */
	#[ORM\Column(type: 'json', nullable: true)]
	private ?array $promoItems = [];

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $heroDesktopImage = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $heroMobileImage = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $heroHeadlineLine1 = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $heroHeadlineLine2 = null;

	#[ORM\Column(type: 'text', nullable: true, columnDefinition: 'LONGTEXT')]
	private ?string $heroSubheadline = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $priceListHeroDesktopImage = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $priceListHeroMobileImage = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $priceListHeroEyebrow = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $priceListHeroTitle = null;

	#[ORM\Column(type: 'text', nullable: true, columnDefinition: 'LONGTEXT')]
	private ?string $priceListHeroLead = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $priceListHeroHighlight1 = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $priceListHeroHighlight2 = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $priceListHeroHighlight3 = null;

	/**
	 * @var array<int, array{title: string, note?: string, items?: array<int, array{icon: string, label: string, url: string, position?: int}>, position?: int}>|null
	 */
	#[ORM\Column(type: 'json', nullable: true)]
	private ?array $contactDetailBlocks = null;

	/**
	 * @var array<int, array{title: string, items?: array<int, array{icon: string, label: string, url: string, position?: int}>, position?: int}>|null
	 */
	#[ORM\Column(type: 'json', nullable: true)]
	private ?array $contactIconBlocks = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $ourEthosTitle = null;

	#[ORM\Column(type: 'text', nullable: true, columnDefinition: 'LONGTEXT')]
	private ?string $ourEthosBody = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $ourEthosImage = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $ourStoryTitle = null;

	#[ORM\Column(type: 'text', nullable: true, columnDefinition: 'LONGTEXT')]
	private ?string $ourStoryBody = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $ourStoryImage = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $consultationTitle = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $consultationEyebrow = null;

	#[ORM\Column(type: 'text', nullable: true, columnDefinition: 'LONGTEXT')]
	private ?string $consultationBody = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $consultationImage = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $consultationButtonLabel = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $bookingTitle = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $bookingEyebrow = null;

	#[ORM\Column(type: 'text', nullable: true, columnDefinition: 'LONGTEXT')]
	private ?string $bookingBody = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $bookingImage = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $bookingButtonLabel = null;

	#[ORM\Column(type: 'text', nullable: true, columnDefinition: 'LONGTEXT')]
	private ?string $footerDescription = null;

	/**
	 * @var array<int, array{icon: string, url: string, label?: string, position?: int}>|null
	 */
	#[ORM\Column(type: 'json', nullable: true)]
	private ?array $footerSocialLinks = null;

	/**
	 * @var array<int, array{sourceKey: string, label?: string, position?: int}>|null
	 */
	#[ORM\Column(type: 'json', nullable: true)]
	private ?array $footerCustomerCareLinks = null;

	/**
	 * @var array<int, array{sourceKey: string, label?: string, position?: int}>|null
	 */
	#[ORM\Column(type: 'json', nullable: true)]
	private ?array $footerCompanyLegalLinks = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @return array<int, array{text: string, url: string}>
	 */
	public function getPromoItems(): array
	{
		$items = is_array($this->promoItems) ? $this->promoItems : [];

		return array_values(array_filter(array_map(static function ($item): ?array {
			if (!is_array($item)) {
				return null;
			}

			$text = trim((string) ($item['text'] ?? ''));
			$url = trim((string) ($item['url'] ?? ''));
			if ($text == '') {
				return null;
			}

			return [
				'text' => $text,
				'url' => $url,
			];
		}, $items)));
	}

	/**
	 * @param array<int, array<string, mixed>>|null $promoItems
	 */
	public function setPromoItems(?array $promoItems): self
	{
		$this->promoItems = $promoItems ?? [];

		return $this;
	}

	public function getHeroDesktopImage(): ?string
	{
		return $this->heroDesktopImage;
	}

	public function setHeroDesktopImage(?string $heroDesktopImage): self
	{
		$this->heroDesktopImage = $heroDesktopImage;

		return $this;
	}

	public function getHeroMobileImage(): ?string
	{
		return $this->heroMobileImage;
	}

	public function setHeroMobileImage(?string $heroMobileImage): self
	{
		$this->heroMobileImage = $heroMobileImage;

		return $this;
	}

	public function getHeroHeadlineLine1(): string
	{
		$value = trim((string) $this->heroHeadlineLine1);

		return $value !== '' ? $value : self::DEFAULT_HERO_HEADLINE_LINE_1;
	}

	public function setHeroHeadlineLine1(?string $heroHeadlineLine1): self
	{
		$this->heroHeadlineLine1 = $this->normalizeNullableText($heroHeadlineLine1);

		return $this;
	}

	public function getHeroHeadlineLine2(): string
	{
		$value = trim((string) $this->heroHeadlineLine2);

		return $value !== '' ? $value : self::DEFAULT_HERO_HEADLINE_LINE_2;
	}

	public function setHeroHeadlineLine2(?string $heroHeadlineLine2): self
	{
		$this->heroHeadlineLine2 = $this->normalizeNullableText($heroHeadlineLine2);

		return $this;
	}

	public function getHeroSubheadline(): string
	{
		$value = trim((string) $this->heroSubheadline);

		return $value !== '' ? $value : self::DEFAULT_HERO_SUBHEADLINE;
	}

	public function setHeroSubheadline(?string $heroSubheadline): self
	{
		$this->heroSubheadline = $this->normalizeNullableText($heroSubheadline);

		return $this;
	}

	public function getPriceListHeroDesktopImage(): ?string
	{
		return $this->priceListHeroDesktopImage;
	}

	public function setPriceListHeroDesktopImage(?string $priceListHeroDesktopImage): self
	{
		$this->priceListHeroDesktopImage = $priceListHeroDesktopImage;

		return $this;
	}

	public function getPriceListHeroMobileImage(): ?string
	{
		return $this->priceListHeroMobileImage;
	}

	public function setPriceListHeroMobileImage(?string $priceListHeroMobileImage): self
	{
		$this->priceListHeroMobileImage = $priceListHeroMobileImage;

		return $this;
	}

	public function getPriceListHeroEyebrow(): string
	{
		$value = trim((string) $this->priceListHeroEyebrow);

		return $value !== '' ? $value : self::DEFAULT_PRICE_LIST_HERO_EYEBROW;
	}

	public function setPriceListHeroEyebrow(?string $priceListHeroEyebrow): self
	{
		$this->priceListHeroEyebrow = $this->normalizeNullableText($priceListHeroEyebrow);

		return $this;
	}

	public function getPriceListHeroTitle(): string
	{
		$value = trim((string) $this->priceListHeroTitle);

		return $value !== '' ? $value : self::DEFAULT_PRICE_LIST_HERO_TITLE;
	}

	public function setPriceListHeroTitle(?string $priceListHeroTitle): self
	{
		$this->priceListHeroTitle = $this->normalizeNullableText($priceListHeroTitle);

		return $this;
	}

	public function getPriceListHeroLead(): string
	{
		$value = trim((string) $this->priceListHeroLead);

		return $value !== '' ? $value : self::DEFAULT_PRICE_LIST_HERO_LEAD;
	}

	public function setPriceListHeroLead(?string $priceListHeroLead): self
	{
		$this->priceListHeroLead = $this->normalizeNullableText($priceListHeroLead);

		return $this;
	}

	public function getPriceListHeroHighlight1(): string
	{
		$value = trim((string) $this->priceListHeroHighlight1);

		return $value !== '' ? $value : self::DEFAULT_PRICE_LIST_HERO_HIGHLIGHT_1;
	}

	public function setPriceListHeroHighlight1(?string $priceListHeroHighlight1): self
	{
		$this->priceListHeroHighlight1 = $this->normalizeNullableText($priceListHeroHighlight1);

		return $this;
	}

	public function getPriceListHeroHighlight2(): string
	{
		$value = trim((string) $this->priceListHeroHighlight2);

		return $value !== '' ? $value : self::DEFAULT_PRICE_LIST_HERO_HIGHLIGHT_2;
	}

	public function setPriceListHeroHighlight2(?string $priceListHeroHighlight2): self
	{
		$this->priceListHeroHighlight2 = $this->normalizeNullableText($priceListHeroHighlight2);

		return $this;
	}

	public function getPriceListHeroHighlight3(): string
	{
		$value = trim((string) $this->priceListHeroHighlight3);

		return $value !== '' ? $value : self::DEFAULT_PRICE_LIST_HERO_HIGHLIGHT_3;
	}

	public function setPriceListHeroHighlight3(?string $priceListHeroHighlight3): self
	{
		$this->priceListHeroHighlight3 = $this->normalizeNullableText($priceListHeroHighlight3);

		return $this;
	}

	public function getContactDetailBlocks(): array
	{
		if ($this->contactDetailBlocks === null) {
			return self::DEFAULT_CONTACT_DETAIL_BLOCKS;
		}

		return $this->normalizeContactDetailBlocks($this->contactDetailBlocks);
	}

	public function setContactDetailBlocks(?array $contactDetailBlocks): self
	{
		$this->contactDetailBlocks = $contactDetailBlocks ?? [];

		return $this;
	}

	public function getContactIconBlocks(): array
	{
		if ($this->contactIconBlocks === null) {
			return self::DEFAULT_CONTACT_ICON_BLOCKS;
		}

		return $this->normalizeContactIconBlocks($this->contactIconBlocks);
	}

	public function setContactIconBlocks(?array $contactIconBlocks): self
	{
		$this->contactIconBlocks = $contactIconBlocks ?? [];

		return $this;
	}

	public function getOurEthosTitle(): string
	{
		$value = trim((string) $this->ourEthosTitle);

		return $value !== '' ? $value : self::DEFAULT_OUR_ETHOS_TITLE;
	}

	public function setOurEthosTitle(?string $ourEthosTitle): self
	{
		$this->ourEthosTitle = $this->normalizeNullableText($ourEthosTitle);

		return $this;
	}

	public function getOurEthosBody(): string
	{
		$value = trim((string) $this->ourEthosBody);

		return $value !== '' ? $value : self::DEFAULT_OUR_ETHOS_BODY;
	}

	public function setOurEthosBody(?string $ourEthosBody): self
	{
		$this->ourEthosBody = $this->normalizeNullableText($ourEthosBody);

		return $this;
	}

	public function getOurEthosImage(): ?string
	{
		return $this->ourEthosImage;
	}

	public function setOurEthosImage(?string $ourEthosImage): self
	{
		$this->ourEthosImage = $ourEthosImage;

		return $this;
	}

	public function getOurStoryTitle(): string
	{
		$value = trim((string) $this->ourStoryTitle);

		return $value !== '' ? $value : self::DEFAULT_OUR_STORY_TITLE;
	}

	public function setOurStoryTitle(?string $ourStoryTitle): self
	{
		$this->ourStoryTitle = $this->normalizeNullableText($ourStoryTitle);

		return $this;
	}

	public function getOurStoryBody(): string
	{
		$value = trim((string) $this->ourStoryBody);

		return $value !== '' ? $value : self::DEFAULT_OUR_STORY_BODY;
	}

	public function setOurStoryBody(?string $ourStoryBody): self
	{
		$this->ourStoryBody = $this->normalizeNullableText($ourStoryBody);

		return $this;
	}

	public function getOurStoryImage(): ?string
	{
		return $this->ourStoryImage;
	}

	public function setOurStoryImage(?string $ourStoryImage): self
	{
		$this->ourStoryImage = $ourStoryImage;

		return $this;
	}

	public function getConsultationTitle(): string
	{
		$value = trim((string) $this->consultationTitle);

		return $value !== '' ? $value : self::DEFAULT_CONSULTATION_TITLE;
	}

	public function setConsultationTitle(?string $consultationTitle): self
	{
		$this->consultationTitle = $this->normalizeNullableText($consultationTitle);

		return $this;
	}

	public function getConsultationEyebrow(): string
	{
		$value = trim((string) $this->consultationEyebrow);

		return $value !== '' ? $value : self::DEFAULT_CONSULTATION_EYEBROW;
	}

	public function setConsultationEyebrow(?string $consultationEyebrow): self
	{
		$this->consultationEyebrow = $this->normalizeNullableText($consultationEyebrow);

		return $this;
	}

	public function getConsultationBody(): string
	{
		$value = trim((string) $this->consultationBody);

		return $value !== '' ? $value : self::DEFAULT_CONSULTATION_BODY;
	}

	public function setConsultationBody(?string $consultationBody): self
	{
		$this->consultationBody = $this->normalizeNullableText($consultationBody);

		return $this;
	}

	public function getConsultationImage(): ?string
	{
		return $this->consultationImage;
	}

	public function setConsultationImage(?string $consultationImage): self
	{
		$this->consultationImage = $consultationImage;

		return $this;
	}

	public function getConsultationButtonLabel(): string
	{
		$value = trim((string) $this->consultationButtonLabel);

		return $value !== '' ? $value : self::DEFAULT_CONSULTATION_BUTTON_LABEL;
	}

	public function setConsultationButtonLabel(?string $consultationButtonLabel): self
	{
		$this->consultationButtonLabel = $this->normalizeNullableText($consultationButtonLabel);

		return $this;
	}

	public function getBookingTitle(): string
	{
		$value = trim((string) $this->bookingTitle);

		return $value !== '' ? $value : self::DEFAULT_BOOKING_TITLE;
	}

	public function setBookingTitle(?string $bookingTitle): self
	{
		$this->bookingTitle = $this->normalizeNullableText($bookingTitle);

		return $this;
	}

	public function getBookingEyebrow(): string
	{
		$value = trim((string) $this->bookingEyebrow);

		return $value !== '' ? $value : self::DEFAULT_BOOKING_EYEBROW;
	}

	public function setBookingEyebrow(?string $bookingEyebrow): self
	{
		$this->bookingEyebrow = $this->normalizeNullableText($bookingEyebrow);

		return $this;
	}

	public function getBookingBody(): string
	{
		$value = trim((string) $this->bookingBody);

		return $value !== '' ? $value : self::DEFAULT_BOOKING_BODY;
	}

	public function setBookingBody(?string $bookingBody): self
	{
		$this->bookingBody = $this->normalizeNullableText($bookingBody);

		return $this;
	}

	public function getBookingImage(): ?string
	{
		return $this->bookingImage;
	}

	public function setBookingImage(?string $bookingImage): self
	{
		$this->bookingImage = $bookingImage;

		return $this;
	}

	public function getBookingButtonLabel(): string
	{
		$value = trim((string) $this->bookingButtonLabel);

		return $value !== '' ? $value : self::DEFAULT_BOOKING_BUTTON_LABEL;
	}

	public function setBookingButtonLabel(?string $bookingButtonLabel): self
	{
		$this->bookingButtonLabel = $this->normalizeNullableText($bookingButtonLabel);

		return $this;
	}

	public function getFooterDescription(): string
	{
		$value = trim((string) $this->footerDescription);

		return $value !== '' ? $value : self::DEFAULT_FOOTER_DESCRIPTION;
	}

	public function setFooterDescription(?string $footerDescription): self
	{
		$this->footerDescription = $this->normalizeNullableText($footerDescription);

		return $this;
	}

	/**
	 * @return array<int, array{icon: string, url: string, label: string, position: int}>
	 */
	public function getFooterSocialLinks(): array
	{
		if ($this->footerSocialLinks === null) {
			return self::DEFAULT_FOOTER_SOCIAL_LINKS;
		}

		return $this->normalizeFooterSocialLinks($this->footerSocialLinks);
	}

	/**
	 * @param array<int, array<string, mixed>>|null $footerSocialLinks
	 */
	public function setFooterSocialLinks(?array $footerSocialLinks): self
	{
		$this->footerSocialLinks = $footerSocialLinks ?? [];

		return $this;
	}

	/**
	 * @return array<int, array{sourceKey: string, label: string, position: int}>
	 */
	public function getFooterCustomerCareLinks(): array
	{
		if ($this->footerCustomerCareLinks === null) {
			return self::DEFAULT_FOOTER_CUSTOMER_CARE_LINKS;
		}

		return $this->normalizeFooterNavigationLinks($this->footerCustomerCareLinks);
	}

	/**
	 * @param array<int, array<string, mixed>>|null $footerCustomerCareLinks
	 */
	public function setFooterCustomerCareLinks(?array $footerCustomerCareLinks): self
	{
		$this->footerCustomerCareLinks = $footerCustomerCareLinks ?? [];

		return $this;
	}

	/**
	 * @return array<int, array{sourceKey: string, label: string, position: int}>
	 */
	public function getFooterCompanyLegalLinks(): array
	{
		if ($this->footerCompanyLegalLinks === null) {
			return self::DEFAULT_FOOTER_COMPANY_LEGAL_LINKS;
		}

		return $this->normalizeFooterNavigationLinks($this->footerCompanyLegalLinks);
	}

	/**
	 * @param array<int, array<string, mixed>>|null $footerCompanyLegalLinks
	 */
	public function setFooterCompanyLegalLinks(?array $footerCompanyLegalLinks): self
	{
		$this->footerCompanyLegalLinks = $footerCompanyLegalLinks ?? [];

		return $this;
	}

	private function normalizeNullableText(?string $value): ?string
	{
		$normalized = trim((string) $value);

		return $normalized !== '' ? $normalized : null;
	}

	/**
	 * @param array<int, mixed>|null $links
	 * @return array<int, array{icon: string, url: string, label: string, position: int}>
	 */
	private function normalizeFooterSocialLinks(?array $links): array
	{
		$items = is_array($links) ? $links : [];
		$normalized = [];

		foreach ($items as $index => $item) {
			if (!is_array($item)) {
				continue;
			}

			$icon = trim((string) ($item['icon'] ?? ''));
			$url = trim((string) ($item['url'] ?? ''));
			$label = trim((string) ($item['label'] ?? ''));
			if ($icon === '' || $url === '') {
				continue;
			}

			$normalized[] = [
				'icon' => $icon,
				'url' => $url,
				'label' => $label,
				'position' => is_numeric((string) ($item['position'] ?? '')) ? (int) $item['position'] : $index,
			];
		}

		usort($normalized, static fn (array $left, array $right): int => $left['position'] <=> $right['position']);

		return array_values($normalized);
	}

	/**
	 * @param array<int, mixed>|null $links
	 * @return array<int, array{sourceKey: string, label: string, position: int}>
	 */
	private function normalizeFooterNavigationLinks(?array $links): array
	{
		$items = is_array($links) ? $links : [];
		$normalized = [];

		foreach ($items as $index => $item) {
			if (!is_array($item)) {
				continue;
			}

			$sourceKey = trim((string) ($item['sourceKey'] ?? ''));
			if ($sourceKey === '') {
				continue;
			}

			$normalized[] = [
				'sourceKey' => $sourceKey,
				'label' => trim((string) ($item['label'] ?? '')),
				'position' => is_numeric((string) ($item['position'] ?? '')) ? (int) $item['position'] : $index,
			];
		}

		usort($normalized, static fn (array $left, array $right): int => $left['position'] <=> $right['position']);

		return array_values($normalized);
	}

	/**
	 * @param array<int, mixed>|null $blocks
	 * @return array<int, array{title: string, note: string, items: array<int, array{icon: string, label: string, url: string, position: int}>, position: int}>
	 */
	private function normalizeContactDetailBlocks(?array $blocks): array
	{
		$items = is_array($blocks) ? $blocks : [];
		$normalized = [];

		foreach ($items as $index => $block) {
			if (!is_array($block)) {
				continue;
			}

			$title = trim((string) ($block['title'] ?? ''));
			if ($title === '') {
				continue;
			}

			$blockItems = $this->normalizeContactBlockItems($block['items'] ?? null);
			if ($blockItems === []) {
				continue;
			}

			$normalized[] = [
				'title' => $title,
				'note' => trim((string) ($block['note'] ?? '')),
				'items' => $blockItems,
				'position' => is_numeric((string) ($block['position'] ?? '')) ? (int) $block['position'] : $index,
			];
		}

		usort($normalized, static fn (array $left, array $right): int => $left['position'] <=> $right['position']);

		return array_values($normalized);
	}

	/**
	 * @param array<int, mixed>|null $blocks
	 * @return array<int, array{title: string, items: array<int, array{icon: string, label: string, url: string, position: int}>, position: int}>
	 */
	private function normalizeContactIconBlocks(?array $blocks): array
	{
		$items = is_array($blocks) ? $blocks : [];
		$normalized = [];

		foreach ($items as $index => $block) {
			if (!is_array($block)) {
				continue;
			}

			$title = trim((string) ($block['title'] ?? ''));
			if ($title === '') {
				continue;
			}

			$blockItems = $this->normalizeContactBlockItems($block['items'] ?? null);
			if ($blockItems === []) {
				continue;
			}

			$normalized[] = [
				'title' => $title,
				'items' => $blockItems,
				'position' => is_numeric((string) ($block['position'] ?? '')) ? (int) $block['position'] : $index,
			];
		}

		usort($normalized, static fn (array $left, array $right): int => $left['position'] <=> $right['position']);

		return array_values($normalized);
	}

	/**
	 * @param array<int, mixed>|null $items
	 * @return array<int, array{icon: string, label: string, url: string, position: int}>
	 */
	private function normalizeContactBlockItems(?array $items): array
	{
		$list = is_array($items) ? $items : [];
		$normalized = [];

		foreach ($list as $index => $item) {
			if (!is_array($item)) {
				continue;
			}

			$icon = trim((string) ($item['icon'] ?? ''));
			$label = trim((string) ($item['label'] ?? ''));
			if ($icon === '' || $label === '') {
				continue;
			}

			$normalized[] = [
				'icon' => $icon,
				'label' => $label,
				'url' => trim((string) ($item['url'] ?? '')),
				'position' => is_numeric((string) ($item['position'] ?? '')) ? (int) $item['position'] : $index,
			];
		}

		usort($normalized, static fn (array $left, array $right): int => $left['position'] <=> $right['position']);

		return array_values($normalized);
	}
}
