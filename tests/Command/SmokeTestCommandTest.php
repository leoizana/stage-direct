<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SmokeTestCommand;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

final class SmokeTestCommandTest extends TestCase
{
    public function testSmokeCommandPasses(): void
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('app_index', new Route('/'));

        $router = $this->createMock(RouterInterface::class);
        $router->method('getRouteCollection')->willReturn($routeCollection);

        $schemaManager = $this->createMock(AbstractSchemaManager::class);
        $schemaManager->method('listTableNames')->willReturn([
            'tbl_activity',
            'tbl_company',
            'tbl_user',
            'tbl_internship',
        ]);

        $result = $this->createMock(Result::class);

        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())->method('executeQuery')->with('SELECT 1')->willReturn($result);
        $connection->method('createSchemaManager')->willReturn($schemaManager);

        $command = new SmokeTestCommand($router, $connection);
        $tester = new CommandTester($command);

        $exitCode = $tester->execute([]);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Smoke test fonctionnel.', $tester->getDisplay());
    }
}