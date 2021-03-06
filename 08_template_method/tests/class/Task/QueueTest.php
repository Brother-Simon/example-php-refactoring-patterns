<?php

class Task_QueueTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Task_Queue
     */
    protected $_task = NULL;

    /**
     * @var DaoMock
     */
    protected $_dao = NULL;

    /**
     * @var MailerMock
     */
    protected $_mailer = NULL;

    /**
     *
     * @var array
     */
    protected $_testData = array(
        'queues' => array(
            1 => array(
                'queueId' => 1,
                'orderNumber' => '2011050101',
                'orderStatus' => Model_Order::PAID,
                'invoiceNumber' => NULL,
                'deliverNumber' => NULL,
            ),
            2 => array(
                'queueId' => 2,
                'orderNumber' => '2011050102',
                'orderStatus' => Model_Order::INVOICE,
                'invoiceNumber' => 'AB12345678',
                'deliverNumber' => NULL,
            ),
            3 => array(
                'queueId' => 3,
                'orderNumber' => '2011050103',
                'orderStatus' => Model_Order::DELIVERED,
                'invoiceNumber' => NULL,
                'deliverNumber' => 'A123456',
            ),
            4 => array(
                'queueId' => 4,
                'orderNumber' => '2011050104',
                'orderStatus' => Model_Order::CLOSED,
                'invoiceNumber' => NULL,
                'deliverNumber' => NULL,
            ),
        ),
        'orders' => array(
            '2011050101' => array(
                'orderNumber' => '2011050101',
                'shopperEmail' => 'tester@example.com',
                'receiverEmail' => 'tester@example.com',
                'invoiceNumber' => NULL,
                'deliverNumber' => NULL,
                'orderStatus' => NULL,
            ),
            '2011050102' => array(
                'orderNumber' => '2011050102',
                'shopperEmail' => 'tester@example.com',
                'receiverEmail' => 'tester@example.com',
                'invoiceNumber' => NULL,
                'deliverNumber' => NULL,
                'orderStatus' => NULL,
            ),
            '2011050103' => array(
                'orderNumber' => '2011050103',
                'shopperEmail' => 'tester@example.com',
                'receiverEmail' => 'tester@example.com',
                'invoiceNumber' => NULL,
                'deliverNumber' => NULL,
                'orderStatus' => NULL,
            ),
            '2011050104' => array(
                'orderNumber' => '2011050104',
                'shopperEmail' => 'tester@example.com',
                'receiverEmail' => 'tester@example.com',
                'invoiceNumber' => NULL,
                'deliverNumber' => NULL,
                'orderStatus' => NULL,
            ),
        ),
    );

    protected function setUp()
    {
        $this->_task = new Task_Queue();
        $this->_dao = new DaoMock($this->_testData);
        $this->_mailer = new MailerMock();

        $this->_task->setDao($this->_dao);
        $this->_task->setMailer($this->_mailer);
    }

    protected function tearDown()
    {
        $this->_task = NULL;
        $this->_dao = NULL;
        $this->_mailer = NULL;
    }

    public function testRun()
    {
        $this->_task->run();

        $taskInfo = $this->_task->getDebugInfo();

        $this->assertArrayHasKey('Status ' . Model_Order::PAID, $taskInfo);
        $this->assertArrayHasKey('Status ' . Model_Order::INVOICE, $taskInfo);
        $this->assertArrayHasKey('Status ' . Model_Order::DELIVERED, $taskInfo);
        $this->assertArrayHasKey('Status ' . Model_Order::CLOSED, $taskInfo);

        $this->assertContains('2011050101', $taskInfo['Status ' . Model_Order::PAID]);
        $this->assertContains('2011050102', $taskInfo['Status ' . Model_Order::INVOICE]);
        $this->assertContains('2011050103', $taskInfo['Status ' . Model_Order::DELIVERED]);
        $this->assertContains('2011050104', $taskInfo['Status ' . Model_Order::CLOSED]);

        $mailerInfo = $this->_mailer->getDebugInfo();
        $this->assertArrayHasKey('訂單 2011050102 發票通知', $mailerInfo);
        $this->assertArrayHasKey('訂單 2011050103 出貨通知', $mailerInfo);
    }
}