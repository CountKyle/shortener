<?php

namespace Shortener;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Nette\Database\Connection;
use Nette\Database\Row;

class App
{
    /**
     * Constructor.
     * 
     * @param Nette\Database\Connection $connection The database layer.
     */
    public function __construct(Connection $connection)
    {
        $this->database = $connection;
    }

    /**
     * Simple migration method.
     */
    public function migrate()
    {
        $this->database->query(
            "DROP TABLE IF EXISTS `links`;

            CREATE TABLE `links` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `url` varchar(1024) DEFAULT '',
              `short_code` varchar(1024) DEFAULT NULL,
              `datetime_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `hits` int NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
            
            LOCK TABLES `links` WRITE;
            INSERT INTO `links` (`id`, `url`, `short_code`, `datetime_created`, `hits`)
            VALUES
                (1,'http://www.google.com','qkt880','2020-12-04 10:09:36',0);
            
            UNLOCK TABLES;"
        );
    }

    /**
     * Processes a long url. 
     * 
     * Searches in the database to see if it exists, otherwise it attempts to create it.
     *
     * @param string $url
     */
    public function processLongUrl(string $url)
    {
        if (strlen(trim($url)) === 0) {
            return $this->message(400, "Please enter url");
        }

        /**
         * Does it already exist?
         */
        $exists = $this->search($url);

        if ($exists) {
            return $this->message(200, "Short url already exists: {$exists->short_code}");
        }

        /**
         * Is it valid and can be resolved?
         */
        try {
            $this->validate($url);
        } catch (Exception $e) {
            return $this->message(400, $e->getMessage());
        } catch (Exception $e) {
            return $this->message(400, $e->getMessage());
        }

        /**
         * Store and return the shortcode.
         */
        $code = $this->generateCode();

        $this->store($url, $code);

        return $this->message(200, "Short code generated: /{$code}");
    }

    /**
     * Processes a short code.
     * 
     * Searches in the database to see if it exists, redirects to the long url if found.
     *
     * @param string $shortCode
     * 
     */
    public function processShortCode(string $shortCode)
    {
        $record = $this->find($shortCode);

        if (!$record) {
            return $this->message(404, "Short code {$shortCode} not found.");
        }

        $this->redirect($record);
    }

    /**
     * Searches for a long url in the database.
     * 
     * @return Nette\Database\Row|null
     */
    private function search(string $url)
    {
        return $this->database->fetch('SELECT * FROM `links` WHERE `url` =?', $url);
    }

    /**
     * Finds a short code.
     * 
     * @param string $shortCode The shortcode to search.
     * 
     * @return Nette\Database\Row|null
     */
    private function find(string $code)
    {
        $code = preg_replace("/[^A-Za-z0-9 ]/", '', $code);
        return $this->database->fetch('SELECT * FROM `links` WHERE `short_code` =?', $code);
    }

    /**
     * Stores a url in the database.
     * 
     * @param string $url The url to store.
     * 
     * @return int
     */
    private function store(string $url, string $shortCode)
    {
        $this->database->query('INSERT INTO links', [
            'url' => $url,
            'short_code' => $shortCode
        ]);

        return $this->database->getInsertId();
    }

    /**
     * Generates a short code.
     * 
     * For a production application we would look for something to ensure uniqueness in our shortcodes, 
     * however base_convert and time is sufficient for this.
     *
     * @return string
     */
    private function generateCode()
    {
        return base_convert(time(), 10, 36);
    }

    /**
     * Validates a url.
     * 
     * @throws Exception
     * 
     * @return boolean
     */
    private function validate($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception("Error - {$url} is not a valid url.");
        }

        if (!$this->checkUrlExists($url)) {
            throw new Exception("Error - {$url} could not be resolved.");
        }

        return true;
    }

    /**
     * Performs a redirect.
     * 
     * @param Nette\Database\Row
     */
    private function redirect(Row $row)
    {
        $this->database->query('UPDATE `links` SET `hits` = `hits` + 1 WHERE `id` = ?', $row->id);

        header("HTTP/1.1 301 Moved Permanently");
        header("Location: {$row->url}", true, 301);
    }

    /**
     * Returns a message to the screen.
     * 
     * @param int $code The http response code.
     * @param string $message The message.
     */
    private function message(int $code = 200, string $message)
    {
        header("HTTP/1.1 {$code}");
        echo $message;
    }

    /**
     * Use Curl to determine whether a url can be resolved.
     * 
     * @param string $url The url to check
     * 
     * @return boolean
     */
    private function checkUrlExists(string $url)
    {
        /**
         * Now, in a real application we might have a better way of 
         * determining whether a url exists... 
         */
        $client = new Client;

        try {
            $client->head($url);
            return true;
        } catch (ClientException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}
