<?php

use KodiComponents\Navigation\Contracts\NavigationInterface;
use KodiComponents\Navigation\Contracts\PageInterface;
use KodiComponents\Navigation\Navigation;
use KodiComponents\Navigation\Page;

class NavigationTest extends TestCase
{
    /**
     * @covers Navigation::__constructor
     */
    public function testConstructor()
    {
        $navigation = new Navigation();

        $this->assertInstanceOf(NavigationInterface::class, $navigation);
        $this->assertEquals(0, $navigation->countPages());
        $this->assertArrayHasKey('pages', $navigation->toArray());
        $this->assertCount(0, $navigation->toArray()['pages']);
    }

    /**
     * @covers Navigation::makePage
     */
    public function testMakePage()
    {
        $page = Navigation::makePage([
            'title' => 'Test',
            'icon' => 'fa fa-user',
            'priority' => 500,
            'url' => 'http://site.com',
            'pages' => [
                [
                    'title' => 'Test 2',
                    'icon' => 'fa fa-group',
                    'url' => 'site.com',
                ],
            ],
        ]);

        $child = $page->getPages()->first();

        $this->assertInstanceOf(PageInterface::class, $page);
        $this->assertInstanceOf(PageInterface::class, $child);

        $this->assertEquals(1, $page->countPages());

        $this->assertEquals('Test', $page->getTitle());
        $this->assertEquals('<i class="fa fa-user"></i>', $page->getIcon());
        $this->assertEquals('http://site.com', $page->getUrl());
        $this->assertEquals(500, $page->getPriority());

        $this->assertEquals('Test 2', $child->getTitle());
        $this->assertEquals('<i class="fa fa-group"></i>', $child->getIcon());
        $this->assertEquals(url('site.com'), $child->getUrl());
        $this->assertEquals(100, $child->getPriority());
    }

    /**
     * @covers Navigation::getCurrentUrl
     */
    public function testGetCurrentUrl()
    {
        $navigation = new Navigation();

        $this->assertEquals(url()->current(), $navigation->getCurrentUrl());

        $navigation->setCurrentUrl('http://site.com/test');
        $this->assertEquals('http://site.com/test', $navigation->getCurrentUrl());
    }

    /**
     * @covers Navigation::setCurrentUrl
     */
    public function testSetCurrentUrl()
    {
        $navigation = new Navigation();

        $navigation->setCurrentUrl('http://site.com/test');
        $this->assertEquals('http://site.com/test', $navigation->getCurrentUrl());
    }

    /**
     * @covers Navigation::setFromArray
     */
    public function testSetFromArray()
    {
        $navigation = new Navigation();

        $navigation->setFromArray([
            [
                'title' => 'Test',
                'icon' => 'fa fa-user',
                'priority' => 500,
                'url' => 'http://site.com',
                'pages' => [
                    [
                        'title' => 'Test3',
                        'icon' => 'fa fa-user',
                        'url' => 'http://site.com',
                    ],
                ],
            ],
            [
                'title' => 'Test1',
                'icon' => 'fa fa-user',
                'priority' => 600,
                'url' => 'http://site.com',
            ],
        ]);

        $this->assertArrayHasKey('pages', $navigation->toArray());
        $this->assertCount(2, $navigation->toArray()['pages']);
        $this->assertEquals('Test', $navigation->toArray()['pages']->first()->getTitle());

        $this->assertEquals(3, $navigation->countPages());

        $navigation->setFromArray([
            [
                'title' => 'Test 4',
                'icon' => 'fa fa-user',
                'priority' => 700,
                'url' => 'http://site.com',
            ],
        ]);

        $this->assertEquals(4, $navigation->countPages());
        $this->assertCount(3, $navigation->toArray()['pages']);
    }

    /**
     * @covers Navigation::addPage
     */
    public function testAddPage()
    {
        $navigation = new Navigation();

        $navigation->addPage('Title');

        $this->assertEquals(1, $navigation->countPages());

        $navigation->addPage([
            'title' => 'Test 4',
            'icon' => 'fa fa-user',
            'priority' => 700,
            'url' => 'http://site.com',
        ]);

        $this->assertEquals(2, $navigation->countPages());

        $navigation->addPage(new Page('Test 5'));

        $this->assertEquals(3, $navigation->countPages());
        $this->assertCount(3, $navigation->toArray()['pages']);
        $this->assertEquals('Title', $navigation->toArray()['pages']->first()->getTitle());
    }

    /**
     * @covers Navigation::getPages
     */
    public function testGetPages()
    {
        $navigation = new Navigation();
        $this->assertInstanceOf(\KodiComponents\Navigation\PageCollection::class, $navigation->getPages());
    }

    /**
     * @covers Navigation::countPages
     */
    public function testCountPages()
    {
        $navigation = new Navigation([
            [
                'title' => 'Title',
                'pages' => [
                    [
                        'title' => 'Title 1',
                        'pages' => [
                            [
                                'title' => 'Title 2',
                                'pages' => [
                                    [
                                        'title' => 'Title 3',
                                        'pages' => [
                                            [
                                                'title' => 'Title 4',
                                                'pages' => [
                                                    [
                                                        'title' => 'Title 5',
                                                        'pages' => [
                                                            [
                                                                new Page('Title 7'),
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Title 8',
            ],
        ]);

        $this->assertEquals(8, $navigation->countPages());
        $this->assertTrue(is_integer($navigation->countPages()));
    }

    /**
     * @covers Navigation::setAccessLogic
     */
    public function testSetAccessLogic()
    {
        $navigation = new Navigation();

        $navigation->setAccessLogic(function ($page) {
            return false;
        });

        $this->assertTrue(is_callable($navigation->getAccessLogic()));
    }

    /**
     * @covers Navigation::getAccessLogic
     */
    public function testGetAccessLogic()
    {
        $navigation = new Navigation();

        $this->assertTrue($navigation->getAccessLogic());

        $navigation->setAccessLogic(function ($page) {
            return false;
        });

        $this->assertTrue(is_callable($navigation->getAccessLogic()));
    }

    /**
     * @covers Navigation::hasChild
     */
    public function testHasChild()
    {
        $navigation = new Navigation([
            [
                'title' => 'Title',
                'pages' => [
                    [
                        'title' => 'Title 1',
                        'pages' => [
                            [
                                'title' => 'Title 2',
                                'pages' => [
                                    [
                                        'title' => 'Title 3',
                                        'pages' => [
                                            [
                                                'title' => 'Title 4',
                                                'pages' => [
                                                    [
                                                        'title' => 'Title 5',
                                                        'pages' => [
                                                            [
                                                                new Page('Title 7'),
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Title 8',
            ],
        ]);

        $this->assertTrue($navigation->hasChild());
        $this->assertTrue($navigation->getPages()->first()->hasChild());
        $this->assertTrue($navigation->getPages()->first()->getPages()->first()->hasChild());
        $this->assertFalse($navigation->getPages()->last()->hasChild());
    }

    /**
     * @covers Navigation::getCurrentPage
     */
    public function testGetCurrentPage()
    {
        $navigation = new Navigation([
            [
                'title' => 'Page 1',
                'url' => 'http://site.com/page-1',
            ],
            [
                'title' => 'Page 2',
                'url' => 'http://site.com/page-2',
                'pages' => [
                    [
                        'title' => 'Page 5',
                        'url' => 'http://site.com/page-2/page-5',
                    ],
                    [
                        'title' => 'Page 6',
                        'url' => 'http://site.com/page-2/page-6',
                    ],
                ],
            ],
            [
                'title' => 'Page 3',
                'url' => 'http://site.com/page-3',
            ],
            [
                'title' => 'Page 4',
                'url' => 'http://site.com/page-3',
            ],
        ]);

        $navigation->setCurrentUrl('http://site.com/page-2/page-5');

        $this->assertInstanceOf(PageInterface::class, $navigation->getCurrentPage());
        $this->assertEquals('Page 5', $navigation->getCurrentPage()->getTitle());

        $navigation->setCurrentUrl('http://site.com/page-3');
        $this->assertEquals('Page 3', $navigation->getCurrentPage()->getTitle());

        $navigation->setCurrentUrl('http://site.com/page');
        $this->assertNull($navigation->getCurrentPage());
    }

    /**
     * @covers Navigation::toArray
     */
    public function testToArray()
    {
        $navigation = new Navigation();

        $this->assertTrue(is_array($navigation->toArray()));
    }

    /**
     * @covers Navigation::filterByAccessRights
     */
    public function testFilterByAccessRights()
    {
        $pages = [
            [
                'title' => 'Page 1',
                'url' => 'http://site.com/page-1',
            ],
            [
                'title' => 'Page 2',
                'url' => 'http://site.com/page-2',
                'pages' => [
                    [
                        'title' => 'Page 5',
                        'url' => 'http://site.com/page-2/page-5',
                    ],
                    [
                        'title' => 'Page 6',
                        'url' => 'http://site.com/page-2/page-6',
                    ],
                ],
            ],
            [
                'title' => 'Page 3',
                'url' => 'http://site.com/page-3',
            ],
            [
                'title' => 'Page 4',
                'url' => 'http://site.com/page-3',
            ],
        ];

        $navigation = new Navigation($pages);

        $this->assertEquals(6, $navigation->countPages());

        $navigation->setAccessLogic(function ($page) {
            return $page->getTitle() == 'Page 2';
        });

        $navigation->filterByAccessRights();

        $this->assertEquals(1, $navigation->countPages());
        $this->assertEquals('Page 2', $navigation->getPages()->first()->getTitle());

        $navigation = new Navigation($pages);
        $navigation->setAccessLogic(function ($page) {
            return $page->getTitle() == 'Page 2' or $page->isChild();
        });
        $navigation->filterByAccessRights();

        $this->assertEquals(3, $navigation->countPages());
        $this->assertEquals(2, $navigation->getPages()->first()->countPages());
        $this->assertEquals('Page 2', $navigation->getPages()->first()->getTitle());
    }

    /**
     * @covers Navigation::sort
     */
    public function testSort()
    {
        $navigation = new Navigation([
            [
                'title' => 'Page 1',
                'priority' => 800,
            ],
            [
                'title' => 'Page 2',
                'priority' => 100,
                'pages' => [
                    [
                        'title' => 'Page 5',
                        'priority' => 300,
                    ],
                    [
                        'title' => 'Page 6',
                        'priority' => 200,
                    ],
                ],
            ],
            [
                'title' => 'Page 3',
                'priority' => 600,
            ],
            [
                'title' => 'Page 4',
                'pages' => [
                    [
                        'title' => 'Page 7',
                        'priority' => 300,
                    ],
                    [
                        'title' => 'Page 8',
                        'priority' => 200,
                    ],
                ],
            ],
        ]);

        $navigation->sort();

        $pages = $navigation->toArray()['pages']->values();
        $this->assertEquals('Page 2', $pages->get(0)->getTitle());
        $this->assertEquals('Page 4', $pages->get(1)->getTitle());
        $this->assertEquals('Page 3', $pages->get(2)->getTitle());
        $this->assertEquals('Page 1', $pages->get(3)->getTitle());
    }
}
