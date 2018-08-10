<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Service;

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;
use Zend\Log\Writer\Stream;
use Zend\Log\Logger;
use GuzzleHttp\Client;

/**
 * Description of AutoruManager
 *
 * @author Daddy
 */
class TelegramManager {

    const COMMANDS_PATH = './vendor/longman/src/Commands/';

    const LOG_FOLDER = './data/log/'; //папка логов
    const LOG_FILE = './data/log/telegramm.log'; //лог 
    
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /*
     * Telegram connectn options
     * @var array
     */
    private $telegramOptions;
    
    public function __construct($entityManager, $telegramOptions)
    {
        $this->entityManager = $entityManager;
        $this->telegramOptions = $telegramOptions;

        if (!is_dir($this::LOG_FOLDER)){
            mkdir($this::LOG_FOLDER);
        }
    }
    
    public function hook()
    {
        
        $writer = new Stream($this::LOG_FILE);
        $logger = new Logger();
        $logger->addWriter($writer);
        Logger::registerErrorHandler($logger);

        try {
            $telegram = new Telegram($this->telegramOptions['options']['access_token'], $this->telegramOptions['options']['username']);
            $telegram->addCommandsPaths($this::COMMANDS_PATH);
            $telegram->enableAdmins($this->telegramOptions['options']['admin_uid']);

            $mysql_credentials = $this->telegramOptions['mysql'];
            $telegram->enableMySql($mysql_credentials, $this::USERNAME . '_');

//            Logging (Error, Debug and Raw Updates)
            Longman\TelegramBot\TelegramLog::initErrorLog($this::LOG_FOLDER . "/".$this::USERNAME."_error.log");
            Longman\TelegramBot\TelegramLog::initDebugLog($this::LOG_FOLDER . "/".$this::USERNAME."_debug.log");
            Longman\TelegramBot\TelegramLog::initUpdateLog($this::LOG_FOLDER . "/".$this::USERNAME."_update.log");

            $telegram->enableLimiter();

            $telegram->handle();

        } catch (Longman\TelegramBot\Exception\TelegramException $e){
            Longman\TelegramBot\TelegramLog::error($e);
        } catch (Longman\TelegramBot\Exception\TelegramLogException $e){
            $logger->error($e->getMessage());            
        }    

        $logger = null;

        return;
    }
    
    public function setHook()
    {
        $writer = new Stream($this::LOG_FILE);
        $logger = new Logger();
        $logger->addWriter($writer);
        Logger::registerErrorHandler($logger);

        try {
            $telegram = new Telegram($this->telegramOptions['options']['access_token'], $this->telegramOptions['options']['username']);
            $result = $telegram->setWebhook($this->telegramOptions['options']['hook_url'], ['certificate' => $this->telegramOptions['options']['certificate']]);
            if ($result->isOk()) {
                echo $result->getDescription();
            }                    
        } catch (Longman\TelegramBot\Exception\TelegramException $e){
            $logger->error($e->getMessage());
        }    

        $logger = null;
        
        return;
    }
    
    public function unsetHook()
    {
        $writer = new Stream($this::LOG_FILE);
        $logger = new Logger();
        $logger->addWriter($writer);
        Logger::registerErrorHandler($logger);

        try {
            $telegram = new Telegram($this->telegramOptions['options']['access_token'], $this->telegramOptions['options']['username']);
            $result = $telegram->deleteWebhook();
            if ($result->isOk()) {
                echo $result->getDescription();
            }             
        } catch (Longman\TelegramBot\Exception\TelegramException $e){
            $logger->error($e->getMessage());
        }    

        $logger = null;
        
        return;
    }
    
    public function sendMessage($params)
    {
        $writer = new Stream($this::LOG_FILE);
        $logger = new Logger();
        $logger->addWriter($writer);
        Logger::registerErrorHandler($logger);           
        \Longman\TelegramBot\TelegramLog::initDebugLog($this::LOG_FILE);

        try {
            $telegram = new Telegram($this->telegramOptions['options']['access_token'], $this->telegramOptions['options']['username']);

            if ($this->telegramOptions['options']['proxy']){
                Request::setClient(new Client([
                    'proxy' => $this->telegramOptions['options']['proxy'],
                    'base_uri' => 'https://api.telegram.org', 
                    'timeout' => 10.0,
                    'cookie' => true,
                ]));
            }    

            $result = Request::sendMessage(['chat_id' => $params['chat_id'], 'text' => $params['text']]);         
        } catch (Longman\TelegramBot\Exception\TelegramException $e){
            $logger->error($e->getMessage());
        }    

        $logger = null;

        return $result;
    }
}