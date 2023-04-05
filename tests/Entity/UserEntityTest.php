<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Traits\AssertTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserEntityTest extends KernelTestCase
{
    use AssertTestTrait;

    protected ?AbstractDatabaseTool $databaseTool = null;

    /**
     * Exécutée avant chaque test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        /* On injecte la class DbToolCollection dans la propriété pour l'utiliser dans les tests */
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testRepositoryCount()
    {
        /* On charge les utilisateurs en base */
        $users = $this->databaseTool->loadAliceFixture([
            \dirname(__DIR__) . '/Fixtures/UserFixtures.yaml',
        ]);

        /* On compte le nombre d'entrée dans la table User */
        $users = self::getContainer()->get(UserRepository::class)->count([]);

        /* On s'attend à avoir 11 users */
        $this->assertEquals(11, $users);
    }

    private function getEntity(): User
    {
        return (new User)
            ->setEmail('test@example.com')
            ->setLastName('Test')
            ->setFirstName('Test')
            ->setRoles(['ROLE_USER'])
            ->setPassword('Test1234');
    }

    public function testValideUserEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testNonUniqueUserEmail()
    {
        $user = $this->getEntity()
            ->setEmail('admin@test.com');

        $this->assertHasErrors($user, 1);
    }

    public function testInvalideEmailUser()
    {
        $user = $this->getEntity()
            ->setEmail('testkuhfhdfdf');

        $this->assertHasErrors($user, 1);
    }

    public function testMinLengthEmailUser()
    {
        $user = $this->getEntity()
            ->setEmail('test');

        $this->assertHasErrors($user, 2);
    }

    public function testMaxLengthEmailUser()
    {
        $user = $this->getEntity()
            ->setEmail('testkdsufhksudhfksdhfksdjfhkdsjfhskdjfhkdjfhkdjhfkdshfkjdhfkjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjfhkdjfhkdjhfkdshfkjdhfkjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjfhkdjfhkdjhfkdshfkjdhfkjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjfhkdjfhkdjhfkdshfkjdhfkjsdhftestkdsufhksudhfksdhfksdjfhkdsjfhskdjfhkdjfhkdjhfkdshfkjdhfkjsdhf@gmail.com');

        $this->assertHasErrors($user, 2);
    }

    public function testEmptyEmailUser()
    {
        $user = $this->getEntity()
            ->setEmail('');

        $this->assertHasErrors($user, 2);
    }

    public function testFisrtNameNotBlankUser()
    {
        $user = $this->getEntity()
            ->setFirstName('');

        $this->assertHasErrors($user, 1);
    }

    public function testFisrtNameMaxLengthUser()
    {
        $user = $this->getEntity()
            ->setFirstName('kusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfvkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsf');

        $this->assertHasErrors($user, 1);
    }

    public function testLastNameNotBlankUser()
    {
        $user = $this->getEntity()
            ->setLastName('');

        $this->assertHasErrors($user, 1);
    }

    public function testLastNameMaxLengthUser()
    {
        $user = $this->getEntity()
            ->setLastName('kusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfvkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsfkusdhfusdhfkjhsdfkjhsdkjfhskdjfhskdjfhkjhdsf');

        $this->assertHasErrors($user, 1);
    }
}
