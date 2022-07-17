<?php

class DebugHelper
{
    //Singleton instance holder
    private static self $instance;
    //Timer
    public static float $timer = 0;
    //Timed action name
    public static string $timedActionDescription;
    //Verbosity of debug ( true | false )
    public static bool $verbose = false;
    //Template ID Iterator
    public static int $tid = 0;

    //Allow singleton instantiation for controlled object scope on gc
    public static function getInstance(bool $verboseFlag = false): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        self::$verbose = $verboseFlag;
        self::$tid++;
        return self::$instance;
    }

    /*
     * Outputs information about the current timer in scope
    */
    public function getTimer(): void
    {
        if (self::$timer === 0) {
            $ti = ['Message' => 'No timer found in scope'];
        } else {
            //Process end
            $endTime = explode(" ", microtime());
            $endTime = $endTime[1] + $endTime[0];
            //Calculate final time
            $total = ($endTime - self::$timer);

            //Build return array
            $ti = [
                'Action' => self::$timedActionDescription,
                'Message' => 'Timer found in scope',
                'Start-Time' => gmdate("Y-m-d H:i:s", substr(self::$timer, 0, 10)),
                'Time-Elapsed' => $total,
            ];
        }
        $this->renderSimpleOutput($ti);
    }

    /*
     * Starts the timer
    */
    public function startTimer($action): void
    {
        //If user attempts to instantiate new timer whilst a timer is in scope
        if (self::$timer == 0) {
            self::$timer = microtime(true);
            self::$timedActionDescription = $action;
        } else {
            $this->getTimer();
        }
    }

    /*
     * Stops the timer and outputs timer info
    */
    public function stopTimer(bool $exit = false): void
    {
        //If timer is null and user attempts to stop
        if (self::$timer == 0) {
            $ti = ['Message' => 'No timer found in scope'];
            $this->renderSimpleOutput($ti);
            return;
        }

        $this->getTimer();
        if ($exit === true) {
            exit();
        }
    }

    /**
     * Output a debug value to a inline template (Simple view)
     */
    public function raw($value, bool $exit = false): void
    {
        $this->renderDebugHeader();
        echo '<pre>';
        var_dump($value);
        echo '</pre>';

    }

    /**
     * Output a debug value to a inline template (Simple view)
     */
    public function dds($value, bool $exit = false): void
    {
        $data = ["DebugValue" => $value];
        $this->renderSimpleOutput($data);
        if ($exit) {
            die("(exit)");
        }
    }

    /**
     * Output a debug value to a inline template
     */
    public function dd($value, $action = ""): void
    {
        $data = ["DebugValue" => $value];
        $this->renderOutput($data, $action);
    }

    /*
    * Render simple output
    * returns @array
    */
    private function renderDebugHeader(): void
    {
        $loader = new \Twig\Loader\FilesystemLoader('src/templates');
        $twig = new \Twig\Environment($loader);
        echo($twig->render('debug/header.twig'));
    }

    /*
    * Render simple output
    * returns @array
    */
    private function renderSimpleOutput($data): void
    {
        $loader = new \Twig\Loader\FilesystemLoader('src/templates');
        $twig = new \Twig\Environment($loader);
        echo($twig->render('debug/simple-debug.twig', ["data" => $data]));
    }

    /*
    * Render output based on config in scope
    * returns @array
    */
    private function renderOutput($data, string $title): void
    {
        $loader = new \Twig\Loader\FilesystemLoader('src/templates');
        $twig = new \Twig\Environment($loader);

        if (self::$verbose === false) {
            echo($twig->render('debug/simple-debug.twig', ["data" => $data]));
        } else {
            self::$tid++;
            //Initialize verbose data array
            $data = [
                "action" => $title,
                "data" => $data,
                "get" => $_GET,
                "post" => $_POST,
                "files" => $_FILES,
                "request" => $_REQUEST,
                "globals" => $GLOBALS,
                "session" => $_SESSION,
                "cookies" => $_COOKIE,
                "global_env" => $_ENV,
                "server" => $_SERVER,
                "tid" => self::$tid
            ];
            //Render verbose debug
            echo($twig->render('debug/debug.twig', ["data" => $data]));
        }
    }
}
