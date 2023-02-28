<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Wish;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
//                'choice_label' => 'name', // => remplacé par la méthode magique __toString de l'entité Category
//                'label' => 'Associated category', // => remplacé par la méthode magique __toString de l'entité Category
                'query_builder' => function(CategoryRepository $categoryRepository){
                    $qb = $categoryRepository->createQueryBuilder('c')->addOrderBy('c.name');
                    return $qb;
                }
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('author', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wish::class,
            'required' => false
        ]);
    }
}
