<?php
/**
 *
 *  * This file is part of the RestUploaderBundle package.
 *  * (c) groovili
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace Groovili\RestUploaderBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Groovili\RestUploaderBundle\Event\FormValidEvent;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class BaseController extends FOSRestController
{
    const MESSAGE_VALIDATION_ERROR = 'Validation error.';

    const MESSAGE_UNKNOWN_ERROR = 'Something went wrong. Try again later.';

    const MESSAGE_BAD_CREDENTIALS = 'Bad credentials.';

    const MESSAGE_SUCCESS = 'Success!';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $form
     * @param $entity
     * @param $data
     * @param array|null $options
     *
     * @return bool|mixed
     */
    protected function processForm(
        Request $request,
        string $form,
        $entity = null,
        $data = null,
        array $options = []
    ) {
        $response = new Response();

        $opt = array_merge([
            'csrf_protection' => false,
            'method' => $request->getMethod(),
        ], (array)$options);

        $form = $this->createForm($form, $entity, $opt);
        $form->handleRequest($request);

        if (null === $data) {
            $data = json_decode($request->getContent(), true);
        }

        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);

        $formEvent = new FormValidEvent($form, $entity, $response);

        if ($form->isSubmitted() && $form->isValid()) {
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(FormValidEvent::FORM_EVENT_VALID, $formEvent);

            return $form->getData();
        }

        return $this->returnValidationError($this->getErrorsFromForm($form));
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return array
     */
    protected function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }

    /**
     * @param string $message
     * @param int $status_code
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function returnValidationError(
        $message = self::MESSAGE_VALIDATION_ERROR,
        $status_code = Response::HTTP_BAD_REQUEST
    ): View {
        $view = $this->view();
        $view->setData($message);
        $view->setStatusCode($status_code);

        return $view;
    }

    /**
     * @param string $message
     * @param int $status_code
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function returnUnknownError(
        $message = self::MESSAGE_UNKNOWN_ERROR,
        $status_code = Response::HTTP_INTERNAL_SERVER_ERROR
    ): View {
        $view = $this->view();
        $view->setData($message);
        $view->setStatusCode($status_code);

        return $view;
    }

    /**
     * @param string $message
     * @param int $status_code
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function returnBadCredentials(
        $message = self::MESSAGE_BAD_CREDENTIALS,
        $status_code = Response::HTTP_UNAUTHORIZED
    ): View {
        $view = $this->view();
        $view->setData($message);
        $view->setStatusCode($status_code);

        return $view;
    }

    /**
     * @param string $message
     * @param int $status_code
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function returnSuccess(
        $message = self::MESSAGE_SUCCESS,
        $status_code = Response::HTTP_OK
    ): View {
        $view = $this->view();
        $view->setData($message);
        $view->setStatusCode($status_code);

        return $view;
    }
}