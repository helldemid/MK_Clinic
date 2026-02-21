<?php

namespace App\Field;

use App\Form\Type\PromoRotatorType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

final class PromoRotatorField implements FieldInterface
{
	use FieldTrait;

	/**
	 * @param TranslatableInterface|string|false|null $label
	 */
	public static function new(string $propertyName, $label = null): self
	{
		return (new self())
			->setProperty($propertyName)
			->setLabel($label)
			->setFormType(PromoRotatorType::class)
			->setTemplateName('crud/field/text')
			->setDefaultColumns('col-md-12');
	}
}
