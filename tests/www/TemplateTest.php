<?php
/**
 * Simple test for syntax-checking Twig-templates.
 *
 * @author Tim van Dijen <tvdijen@gmail.com>
 * @package SimpleSAMLphp
 */
 
namespace SimpleSAML\Test\Web;

use \SimpleSAML\Configuration as Configuration;
use \SimpleSAML\XHTML_Template as Template;
use \SimpleSAML\Module;

class TemplateTest extends \PHPUnit\Framework\TestCase
{
    public function testSyntax()
    {
        $config = Configuration::loadFromArray([
            'language.i18n.backend' => 'gettext/gettext',
            'module.enable' => array_fill_keys(Module::getModules(), true),
        ]);

        Configuration::setPreLoadedConfig($config);
        $basedir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'templates';

        // Base templates
        $files = array_diff(scandir($basedir), array('.', '..'));
        foreach ($files as $file) {
            if (preg_match('/.twig$/', $file)) {
                $t = new Template($config, 'monitor:'.basename($file));
                ob_start();
                try {
                    $t->show();
                    $this->addToAssertionCount(1);
                } catch (\Twig_Error_Syntax $e) {
                    $this->fail($e->getMessage().' in '.$e->getFile().':'.$e->getLine());
                }
                ob_end_clean();
            }
        }
    }
}
