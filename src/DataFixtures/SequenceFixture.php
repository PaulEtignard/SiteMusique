<?php

namespace App\DataFixtures;

use App\Entity\Sequence;
use App\Repository\NiveauRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class SequenceFixture extends Fixture
{
    private SluggerInterface $slugger;
    private NiveauRepository $niveauRepository;

    /**
     * @param SluggerInterface $slugger
     * @param NiveauRepository $niveauRepository
     */
    public function __construct(SluggerInterface $slugger, NiveauRepository $niveauRepository)
    {
        $this->slugger = $slugger;
        $this->niveauRepository = $niveauRepository;
    }


    public function load(ObjectManager $manager): void
    {
        $minIdNiveau = $this->niveauRepository->findOneBy([],["id"=>"asc"]);
        $maxIdNiveau = $this->niveauRepository->findOneBy([],["id"=>"desc"]);


        $faker = Factory::create("fr_FR");
        for ($i=1;$i<15;$i++){
            $sequence = New Sequence();
            $sequence->setNom($faker->word)
                ->setSlug($this->slugger->slug($sequence->getNom())->lower())
                ->setCreatedAt(new \DateTime())
                ->setNiveau($this->niveauRepository->findOneBy(["id"=>$faker->numberBetween(1,5)]));
            $this->setReference("sequence".$i,$sequence);

            $niveau = $sequence->getNiveau();
            $niveau->addSequence($sequence);
            $manager->persist($niveau);
            $manager->persist($sequence);

        }
        $manager->flush();
    }
}
