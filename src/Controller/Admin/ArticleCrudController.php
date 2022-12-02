<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Repository\SequenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleCrudController extends AbstractCrudController
{
    private SluggerInterface $slugger;
    private SequenceRepository $sequenceRepository;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger, SequenceRepository $sequenceRepository)
    {
        $this->slugger = $slugger;
        $this->sequenceRepository = $sequenceRepository;

    }


    public static function getEntityFqcn(): string
    {
        return Article::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $sequences = $this->sequenceRepository->findAll();
        $sequenceArray = [];
        foreach ($sequences as $sequence){
            $sequenceArray = $sequenceArray + [$sequence->getNom()." / ".$sequence->getNiveau()->getIntitule() => $sequence];
        }
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('titre'),
            TextEditorField::new('contenu'),
            ChoiceField::new('sequence')->onlyOnForms()->autocomplete()->setChoices($sequenceArray),
            DateField::new('createdAt')->onlyOnIndex()
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Article) return;
        $entityInstance->setCreatedAt(new \DateTime());
        $entityInstance->setSlug($this->slugger
            ->slug($entityInstance->getTitre())->lower());
        parent::persistEntity($entityManager,$entityInstance);
    }


}
