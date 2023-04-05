<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Article;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Tests\Traits\AssertTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class ArticleEntityTest extends KernelTestCase
{
    use AssertTestTrait;

    protected ?AbstractDatabaseTool $databaseTool = null;

    /**
     * Exécutée avant chaque test.
     */
    public function setUp(): void
    {
        parent::setUp();

        /* On injecte la class DbToolCollection dans la propriété pour l'utiliser dans les tests */
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testRepositoryCount()
    {
        /* On charge les articles en base */
        $articles = $this->databaseTool->loadAliceFixture([
            \dirname(__DIR__) . '/Fixtures/UserFixtures.yaml',
            \dirname(__DIR__) . '/Fixtures/ArticleFixtures.yaml',
        ]);

        /* On compte le nombre d'entrée dans la table User */
        $articles = self::getContainer()->get(ArticleRepository::class)->count([]);

        /* On s'attend à avoir 11 users */
        $this->assertEquals(21, $articles);
    }

    private function getEntity(): Article
    {
        $user = self::getContainer()->get(UserRepository::class)->find(1);

        return (new Article)
            ->setTitle('Mon article de test')
            ->setAuthor($user)
            ->setEnabled(true)
            ->setContent('Mon super article');
    }

    public function testValideArticleEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testNonUniqueTitleArticle()
    {
        $article = $this->getEntity()
            ->setTitle('Article de test');

        $this->assertHasErrors($article, 1);
    }

    public function testNotBlankTitleArticle()
    {
        $article = $this->getEntity()
            ->setTitle('');

        $this->assertHasErrors($article, 1);
    }

    public function testMaxLengthTitleArticle()
    {
        $article = $this->getEntity()
            ->setTitle('sldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdgsldkjflksdjflksdjflksdjflksjdflkjdsflkjdkldlkjdg');

        $this->assertHasErrors($article, 1);
    }
}
