<?php
namespace Dangoodman\ComposerForWordpress;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;


class ComposerForWordpress implements PluginInterface, EventSubscriberInterface
{
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_AUTOLOAD_DUMP => array(
                array('onPostAutoloadDump', 0)
            ),
        );
    }

    public function onPostAutoloadDump(Event $event)
    {
        $composerConfig = $event->getComposer()->getConfig();
        $composerAutoloadDir = "{$composerConfig->get('vendor-dir')}/composer";

        $classLoader = "{$composerAutoloadDir}/ClassLoader.php";
        $autoloadReal = "{$composerAutoloadDir}/autoload_real.php";
        $autoloadStatic = "{$composerAutoloadDir}/autoload_static.php";

        $suffix = $composerConfig->get('classloader-suffix') ?: md5(uniqid('', true));

        self::replaceInFiles(
            array($classLoader, $autoloadReal),
            '/Composer\\\\Autoload(;|\\\\(?!ComposerStaticInit))/',
            "Composer\\Autoload{$suffix}\$1"
        );

        self::replaceInFiles(
            array($autoloadStatic),
            array(
                '/\bClassLoader\b/'
                    => "ClassLoader{$suffix}",
                '/'.preg_quote("\nnamespace Composer\\Autoload;\n", '/').'/'
                    => "$0\nuse Composer\\Autoload{$suffix}\\ClassLoader as ClassLoader{$suffix};\n\n",
            )
        );
    }

    private static function replaceInFiles(array $files, $search, $replace = null)
    {
        if (func_num_args() == 3) {
            $search = array($search => $replace);
        }

        foreach ($files as $file) {
            $contents = file_get_contents($file);
            $contents = preg_replace(array_keys($search), array_values($search), $contents);
            file_put_contents($file, $contents);
        }
    }
}
