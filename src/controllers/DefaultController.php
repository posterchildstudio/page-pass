<?php
/**
 * Page Password plugin for Craft CMS 3.x
 *
 * A simple plugin that allows you to password protect a page.
 *
 * @link      https://www.jesseward.com
 * @copyright Copyright (c) 2018 Jesse Ward
 */

namespace jesseward\pagepassword\controllers;

use jesseward\pagepassword\PagePassword;

use Craft;
use craft\web\Controller;
use craft\elements\Entry;

/**
 * @author    Jesse Ward
 * @package   PagePassword
 * @since     0.1.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['authorise'];

    public function actionAuthorise()
    {
        $request = Craft::$app->getRequest();
        $password = $request->getBodyParam('password');
        $id = $request->getBodyParam('id');
        $pageId = $request->getBodyParam('pageId');
        $pagePassword = Entry::find()->id($pageId)->one()->pagePassword;

        if ($this->passwordIsValid($password, $pagePassword)) {
            $expires = time() + (60*60*24*7*2); // Two weeks
            setcookie(md5($id), 1, $expires, '/');
        } else {
            Craft::$app->getSession()->addFlash('error', 'Invalid password - please try again', false);
        }

        // TODO: Check if this needs to be more secure
        $url = $request->getBodyParam('redirect');

        return $this->redirect($url);
        // return var_dump($pagePassword);
    }

    private function passwordIsValid($password, $pagePassword)
    {
        // $validPassword = getenv('PAGE_PASSWORD');
        $validPassword = $pagePassword;
        return ($password === $validPassword);
    }
}
