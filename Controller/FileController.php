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
use Groovili\RestUploaderBundle\Entity\File;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FileController
 * @package Groovili\RestUploaderBundle\Controller
 */
class FileController extends FOSRestController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \FOS\RestBundle\View\View|null
     *
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
        $this->denyAccessUnlessGranted(User::ROLE);

        $uploader = $this->get('app.file_uploader');
        $image = $request->files->get('file');

        if (isset($image)) {
            try {
                if (!$uploader->checkFileSize($image)) {
                    return $this->returnValidationError('File is too big or not valid.');
                }

                $file = $uploader->uploadFile($image);

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                return $this->returnSuccess($file, Response::HTTP_CREATED);
            } catch (FileException $e) {
                return $this->returnUnknownError($e->getMessage());
            }
        } else {
            return $this->returnValidationError('File was not set.');
        }
    }

    /**
     * @return View
     *
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
    public function cgetAction(Request $request)
    {
        $this->denyAccessUnlessGranted(User::ROLE);

        $repo = $this->getDoctrine()->getRepository('AppBundle:File');

        $offset = (int)$request->get('page') * $repo->getItemsPerPage();

        $files = $repo->all($offset);

        return $this->returnSuccess($files);
    }

    /**
     * @param File $file
     *
     * @return File
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
    public function getAction(File $file)
    {
        $this->denyAccessUnlessGranted(User::ROLE);

        return $file;
    }

    /**
     * @param \AppBundle\Entity\File $file
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
        $this->denyAccessUnlessGranted(User::ROLE);

        $uploader = $this->get('app.file_uploader');

        $download = file_get_contents($uploader->getFileRealPath($file));

        $headers = [
            'Content-Type' => $file->getFormat(),
            'Content-Disposition' => 'inline; filename="' . $file->getName() . '"',
        ];

        return new Response($download, 200, $headers);
    }

    /**
     * @param File $file
     *
     * @return View
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
    public function deleteAction(File $file)
    {
        $doctrine = $this->getDoctrine();

        $relatedUsers = $doctrine->getRepository('AppBundle:User')->findBy([
            'file' => $file,
        ]);

        foreach ($relatedUsers as $user) {
            $user->removeFile();
        }

        $em = $doctrine->getManager();
        $em->remove($file);
        $em->flush();

        return $this->returnSuccess();
    }
}