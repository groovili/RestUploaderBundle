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

namespace Groovili\RestUploaderBundle\Event;

use Symfony\Component\Form\FormEvent as BaseFormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FormValidEvent
 */
class FormValidEvent extends BaseFormEvent
{
    CONST FORM_EVENT_VALID = 'app.form.valid';

    /**
     * @var Response
     */
    protected $response;

    /**
     * FormEvents constructor.
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @param mixed $data
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function __construct(FormInterface $form, $data, Response $response)
    {
        parent::__construct($form, $data);
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}