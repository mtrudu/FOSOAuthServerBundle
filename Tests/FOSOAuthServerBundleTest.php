<?php

declare(strict_types=1);

/*
 * This file is part of the FOSOAuthServerBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\OAuthServerBundle\Tests;

use FOS\OAuthServerBundle\DependencyInjection\Compiler;
use FOS\OAuthServerBundle\DependencyInjection\Security\Factory\OAuthFactory;
use FOS\OAuthServerBundle\FOSOAuthServerBundle;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FOSOAuthServerBundleTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testConstruction(): void
    {
        $bundle = new FOSOAuthServerBundle();

        /** @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject $containerBuilder */
        $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getExtension',
                'addCompilerPass',
            ])
            ->getMock()
        ;

        /** @var SecurityExtension|\PHPUnit_Framework_MockObject_MockObject $securityExtension */
        $securityExtension = $this->getMockBuilder(SecurityExtension::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $containerBuilder
            ->expects($this->any())
            ->method('getExtension')
            ->with('security')
            ->willReturn($securityExtension)
        ;

        $securityExtension
            ->expects($this->any())
            ->method('addAuthenticatorFactory')
            ->with(new OAuthFactory())
            ->willReturn(null)
        ;

        $containerBuilder
            ->method('addCompilerPass')
            ->withConsecutive(
                [ new Compiler\GrantExtensionsCompilerPass() ],
                [ new Compiler\RequestStackCompilerPass() ]
             )
            ->willReturnOnConsecutiveCalls(
                $containerBuilder,
                $containerBuilder
            )
        ;

        $this->assertNull($bundle->build($containerBuilder));
    }
}
