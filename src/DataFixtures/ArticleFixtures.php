<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Repository\SequenceRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
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

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        for($i=1;$i<100;$i++){
            $article = new Article();
            $article->setTitre($faker->word)
                ->setSlug($this->slugger->slug($article->getTitre())->lower())
                ->setCreatedAt(New \DateTime())
                ->setContenu($faker->paragraph($faker->numberBetween(7,15)))
                ->setSequence($this->getReference("sequence".$faker->numberBetween(1,14)));
                $manager->persist($article);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return[
          SequenceFixture::class
        ];
    }
}
