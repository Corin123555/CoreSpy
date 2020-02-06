# CoreSpy
 
## What is it?
CoreSpy is intended to be a self contained GameSpy replacement service written in PHP using Swoole.

This includes all relative servers (master server, presence server etc) and a webui to complement it.

Alongside this, the goal is to document as much of the protocol as possible and cite them using public SDKs.

### Why Laravel
Laravel has a lot of perks such as the Eloquent ORM and the fact that as a framework it works very nicely with building CLI elements.

That and it keeps the entire system contained, both the webui aspect and the server aspect.

### Does it work?

Kinda?

There's quite a few elements of the project that aren't working and probably could do with someone with a bigger brain than me to take a look and see where I went wrong.
Information on why those features are broken/not working can be found within those files in their comments

This includes:
* GameSpy password decoding (and probably encoding) (app\GameSpy\Common\Utils\GSPassword.php)
* GameSpy EncTypeX decoding and encoding (app\GameSpy\Common\Utils\GSEncTypeX.php)

There's calls missing from various servers (such as newuser/login/status etc) as I haven't ported them over from a local server but that will hopefully come in the following days.

Most commands/calls get mapped as they're called from the game I'm working against.

### So what does work?

The master server is somewhat done from a server registration POV I believe?

Testing against Halo: CE and Area 51, the games follow the standard gamespy flow.

Honestly a lot of stuff wont be able to be tested until EncTypeX and Password functionality is complete though.

## What's required?
You'll need:
* PHP 7.1+
* [Swoole](https://www.swoole.co.uk/) [(easy to install)](https://www.swoole.co.uk/docs/get-started/installation)
* php-mbstring
* php-json
* php-mysql

## How do I run this?
With all the above requirements installed:
* Perform a `composer install`
* Perform a db migrate by `php artisan migrate`
* Start the respective servers (this will need to be done within a screen or your preferred tool for now):
    * GSMS (MasterServer): `php artisan gs:startms`
    * GPCM (Connection Manager): `php artisan gs:startgpcm`
    * GPSP (Presence): `php artisan gs:startgpsp`

## Credits
A huge thanks to the friends who've helped me with the project, alongside:
* Luigi Auriemma (http://aluigi.altervista.org/) for his writeups/examples
* OpenSpy (https://github.com/devzspy/GameSpy-Openspy-Core) for a few extra examples