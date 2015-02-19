<?php
/**
 * This file is part of CSBill package.
 *
 * (c) 2013-2014 Pierre du Plessis <info@customscripts.co.za>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace CSBill\PaymentBundle\Form;

use CSBill\PaymentBundle\Repository\PaymentMethodRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'payment_method',
            'entity',
            array(
                'class' => 'CSBillPaymentBundle:PaymentMethod',
                'query_builder' => function (PaymentMethodRepository $repository) use ($options) {
                    $queryBuilder = $repository->createQueryBuilder('pm');
                    $expression = new Expr();
                    $queryBuilder->where($expression->eq('pm.enabled', 1));

                    // If user is not logged in, exclude internal payment methods
                    if (null === $options['user']) {
                        $queryBuilder->AndWhere($expression->eq('pm.internal', 0));
                    }

                    return $queryBuilder;
                },
                'required' => true,
                'constraints' => new NotBlank(),
                'placeholder' => 'Choose Payment Method',
                'attr' => array(
                    'class' => 'select2',
                ),
            )
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('user'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'payment';
    }
}