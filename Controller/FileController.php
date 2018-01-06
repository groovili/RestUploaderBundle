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

use Groovili\RestUploaderBundle\Entity\File;
use FOS\RestBundle\View\View;
use Groovili\RestUploaderBundle\Form\Type\RestFileType;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FileController
 *
 * @package Groovili\RestUploaderBundle\Controller
 */
class FileController extends BaseController
{
    /**
     * @SWG\Post(
     * tags={"file"},
     * @SWG\Parameter(
     *     name="file",
     *     in="formData",
     *     type="file",
     *     required=true,
     *     description="File to be uploaded."
     * ),
     * @SWG\Response(
     *     response=201,
     *     description="Success, file uploaded.",
     *     @SWG\Schema(
     *      type="array",
     *      @Model(type=File::class)
     *     ),
     * ),
     * @SWG\Response(
     *     response=400,
     *     description="Something went wrong while uploading."
     * ),
     * @SWG\Response(
     *     response=500,
     *     description="Authentication needed!"
     * ),
     * )
     */
    public function uploadAction(Request $request): ?View
    {
        $upload = $request->files->get('file');

        if (isset($upload)) {
            $form = $this->createFormBuilder()
                ->add('file', RestFileType::class, [
                    'allow_delete' => true,
                    'validate_extensions' => true,
                    'validate_size' => true,
                    'private' => false,
                ])
                ->getForm();

            $form->handleRequest($request);
            $clearMissing = $request->getMethod() != 'PATCH';
            $form->submit(['file' => $upload], $clearMissing);

            $data = $form->getData();

            if (isset($data['file'])) {
                $file = $data['file'];
                $em = $this->getDoctrine()->getManager();
                $em->persist($file);
                $em->flush();

                return $this->returnSuccess($file);
            }

            return $this->returnValidationError($data);
        }

        return $this->returnValidationError('File was not set.');
    }

    /**
     * @SWG\Get(
     * tags={"file"},
     * @SWG\Parameter(
     *    name="page",
     *    in="query",
     *    type="integer",
     *    required=false,
     *    description="Page number."
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Success, list of files",
     *     @SWG\Schema(
     *      type="array",
     *      @Model(type=File::class)
     *     ),
     * ),
     * @SWG\Response(
     *     response=500,
     *     description="Authentication needed!"
     * ),
     * )
     */
    public function cgetAction(Request $request): ?View
    {
        $repo = $this->getDoctrine()
            ->getRepository('RestUploaderBundle:File');

        $offset = (int)$request->get('page') * $repo->getItemsPerPage();

        $files = $repo->all($offset);

        return $this->returnSuccess($files);
    }

    /**
     * @SWG\Get(
     * tags={"file"},
     * @SWG\Parameter(
     *    name="file",
     *    in="query",
     *    type="integer",
     *    required=true,
     *    description="File id."
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="Returns the file by id",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=File::class)
     *     )
     * ),
     * @SWG\Response(
     *     response=404,
     *     description="Returns when file is not found"
     * ),
     * @SWG\Response(
     *     response=500,
     *     description="Authentication needed!"
     * ),
     * )
     */
    public function getAction(File $file): ?View
    {
        return $this->returnSuccess($file);
    }

    /**
     * @param File $file
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     *
     * @SWG\Get(
     * tags={"file", "download"},
     * @SWG\Parameter(
     *    name="file",
     *    in="query",
     *    type="integer",
     *    required=true,
     *    description="File id."
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="File download"
     * ),
     * @SWG\Response(
     *     response=404,
     *     description="File object not found with given id"
     * ),
     * @SWG\Response(
     *     response=500,
     *     description="Authentication needed!"
     * ),
     * )
     */
    public function downloadAction(File $file): Response
    {
        $manager = $this->get('rest_uploader.manager');

        return $manager->download($file);
    }

    /**
     * @SWG\Delete(
     * tags={"file"},
     * @SWG\Parameter(
     *    name="file",
     *    in="query",
     *    type="integer",
     *    required=true,
     *    description="File id."
     * ),
     * @SWG\Response(
     *     response=204,
     *     description="File deleted"
     *
     * ),
     * @SWG\Response(
     *     response=404,
     *     description="File object not found with given id"
     * ),
     * @SWG\Response(
     *     response=500,
     *     description="Authentication needed!"
     * ),
     * )
     */
    public function deleteAction(File $file): ?View
    {
        $manager = $this->get('rest_uploader.manager');

        if (!$manager->remove($file)) {
            return $this->returnUnknownError('Failed to remove file.');
        }

        $doctrine = $this->getDoctrine();

        $em = $doctrine->getManager();
        $em->remove($file);
        $em->flush();

        return $this->returnSuccess();
    }
}