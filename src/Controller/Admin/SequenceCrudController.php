<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Sequence;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class SequenceCrudController extends AbstractCrudController
{
    private SluggerInterface $slugger;
    private NiveauRepository $niveauRepository;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger, NiveauRepository $niveauRepository)
    {
        $this->slugger = $slugger;
        $this->niveauRepository = $niveauRepository;
    }


    public static function getEntityFqcn(): string
    {
        return Sequence::class;
    }
    public function configureFields(string $pageName): iterable
    {
        $niveaux = $this->niveauRepository->findAll();
        $array = [];
        foreach ($niveaux as $niveau){
            $array = $array + [$niveau->getIntitule() => $niveau];
        }

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom'),
            ChoiceField::new('niveau')->onlyOnForms()->autocomplete()->setChoices($array),
            DateField::new('createdAt')->onlyOnIndex()
        ];
    }


    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Sequence) return;
        $entityInstance->setCreatedAt(new \DateTime());
        $entityInstance->setSlug($this->slugger
            ->slug($entityInstance->getNom())->lower());
        parent::persistEntity($entityManager,$entityInstance);
    }
}
