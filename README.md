# Symfony Boilerplate

## What the duck is this?
Imagine you just came up with a new greatest idea ever and can't wait to think it
through. You believe in code! You're the programmer! Aye, lets start coding! Now!

So, if you're reading this, chances are you're going to build next one billion $ app
with Symfony, the 4, because why not, Mark did it in PHP so can you!

![screen1.png](https://github.com/cepa/symfony-boilerplate/blob/master/docs/screen1.png)

But, hold on my friend... ha ha ha... need to set up that god damned Symfony stack
first. And hey, its PHP, can't just run PHP, need bloody web server. And a database.
And ideally, your new billion $ app will need some users, so User entity and perhaps
_you_ the _uber admin_ of the newly created universe. And you know, Docker... hipsters
love Docker and Docker and a bunch of other marketing bullshit words could increase
your valuation, so... why not.

## This project, is the _solution_! 
![screen2.png](https://github.com/cepa/symfony-boilerplate/blob/master/docs/screen2.png)
You can clone it and focus on what you really wanna do - coding the app, without 
having to glue the bloody framework components first and mess with any DevOpsish stuff.

## Why had I created this?
Because I've just spent 5 hours of my life on bootstraping a fucking framework...
![screen3.png](https://github.com/cepa/symfony-boilerplate/blob/master/docs/screen3.png)
And it had taken me a shitload more time when I did it the first time, duh.

## Anyway
Here's how to use this crap, clone it first though.

### Build and run
You can find the docker-compose.yml here, but uhm... docker-compose is just too long
command to my taste, so I wrapped it around with Make, so:
~~~
make build down up
~~~
It will build Docker images with PHP, MySQL and Nginx and start them with the _/portal_
directory mounted to the PHP and Nginx containers, so no need to reload Docker when
to run changes.

## Install and purge
Life would be too easy if you hadn't to set up a database, so here it goes:
~~~
make purge install
~~~
First, _purge_ will remove the old DB and _install_ will create it again with 
Doctrine to build the structure and populate it with some data with Doctrine Fixtures.

## Need logs?
What is life without logs? 
~~~
make logs
~~~
That will tail to all containers logs, should be helpful enough.

## Testing
We all know when we test we test in prod, but... just for clarity sake:
~~~
make test
~~~
No, its not just PHPUnit, because guess what... it takes just too fucking long to wait
for a PHPUnit process to run _all_ test, its not 1990 anymore, my shitty laptop can run 8
threads in parallel now. Test are run with Paratest which is a parallel PHPUnit.

## Endpoints
Assumming you're lucky enough to actually run this crap successfully, you can open
the following links in your browser:
_Portal_
- http://localhost:55080/
_Admin_
- http://localhost:55080/admin
admin@domain.tld / s3cr3t

## What is included
- Symfony 4
- Easyadmin for _/admin_
- Doctrine ORM
- Doctrine Fixtures
- Twig
- PHPUnit (Paratest)
- Symfony Encore (for frontend stuff)
- Docker
- PHP 7.2 (FPM)
- Nginx
- MySQL

## Found a bug or fancy adding something?
Feel free to send me a pull request.

## License
You can buy me a beer when you meet me. I mean, scotch's good either ;)
