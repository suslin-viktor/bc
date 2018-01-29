<img src="https://github.com/wielski/PHPThreads/blob/master/phpthreads.png?raw=true">

PHP Threads, PHP 5.3+
==========

This class makes it easy to implement multithreading in PHP.<br/>
It works on any hosting, the only requirement is curl.<br/>
by Andrzej Wielski <a href="http://vk.com/wielski"><img src="http://images2.wikia.nocookie.net/__cb20130815064125/gnomoria/ru/images/b/b4/Vk_favicon.png"></a>

<hr/>

<h4>Including the class</h4>
`require_once 'lib/Threads.php';`

<hr/>
<h4>Creating a Thread</h4>

    $Thread->Create(function(){
   	sleep(5);
   	echo 'Sleep 5';
    });

<hr/>

<h4>Starting Threads</h4>

`$Thread->Run();`

<hr/>

<h4>Starting Threads without printing anything from thread</h4>
`$Thread->Run(false);`
This code will run, but nothing will shown for user.<br/>
`print_r($Thread->Run(false));`
This code will run, and we get only responses from threads (return).

<hr/>

<h4>Transfer variables to the thread</h4>

    $Thread->Create(function($vars) use ($a){
     echo $a;
    });

This code will print `$a`

<hr/>

<h4>Security</h4>
You should change password and salt in file "lib/Threads.php"<br />
`private $password = '785tghjguigu';
private $salt = 'DfEQn8*#^2n!9jErF';`<br />
This is the password for encrypting the functions

<br />
<br />
<a href="https://github.com/wielski/PHPThreads/releases"><img src="https://assets-cdn.github.com/images/modules/logos_page/Octocat.png" width="24"> Download</a>
