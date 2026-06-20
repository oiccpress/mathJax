<?php

/**
 * Main class for letter of acceptance plugin
 * 
 * @author Joe Simpson
 * 
 * @class MathJaxPlugin
 *
 * @brief MathJaxPlugin
 */

namespace APP\plugins\generic\mathJax;

use APP\core\Request;
use APP\core\Application;
use APP\plugins\generic\letterOfAcceptance\classes\Settings\Actions;
use APP\plugins\generic\letterOfAcceptance\classes\Settings\Manage;
use APP\template\TemplateManager;
use PKP\core\JSONMessage;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use PKP\security\Role;

class MathJaxPlugin extends GenericPlugin {

    public $hasMathJax = false;

    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path);

        if ($success && $this->getEnabled()) {
            
            $request = Application::get()->getRequest();
            $templateMgr = TemplateManager::getManager($request);
            $templateMgr->registerPlugin('modifier', 'mathjax_render', $this->renderMathJax(...));

        }

        return $success;
    }

    /**
     * Provide a name for this plugin
     *
     * The name will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDisplayName()
    {
        return __('plugins.generic.mathJax.displayName');
    }

    /**
     * Provide a description for this plugin
     *
     * The description will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDescription()
    {
        return __('plugins.generic.mathJax.description');
    }

    public function renderMathJax(string $input) {
        $re = '/&lt;math.+&lt;\/math&gt;/m';

        $input = preg_replace_callback($re, function($matches) {
            if(!$this->hasMathJax) {
                $request = Application::get()->getRequest();
                $templateMgr = TemplateManager::getManager($request);
                $MathJaxScript = '</script><script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@4/tex-mml-chtml.js">';
                $templateMgr->addJavaScript('mathjax', $MathJaxScript, 
                    array(
                        'inline' => true,
                        'contexts' => array('frontend', 'backend')
                    )
                );
            }
            return strip_tags( html_entity_decode($matches[0]), [
                'math', 'mi', 'mo', 'mfrac', 'mrow', 'msqrt', 'mn',
                'munder', 'munderover', 'mtr', 'mtext', 'mtd', 'mtable',
                'msubsup', 'msup', 'msub', 'mstyle', 'mspace', 'semantics',
                'ms', 'mroot', 'multiscripts', 'mfenced', 'menclose', 'merror',
                'annotation-xml', 'annotation', 'maction',
            ] );
        }, $input);

        return $input;
    }

}
