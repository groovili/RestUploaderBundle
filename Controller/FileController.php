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
use Groovili\RestUploaderBundle\Form\Type\RestFileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class FileController
 *
 * @package Groovili\RestUploaderBundle\Controller
 */
class FileController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function uploadAction(Request $request): Response
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

                $encoder = new JsonEncoder();
                $normalizer = new ObjectNormalizer();
                $serializer = new Serializer([$normalizer], [$encoder]);

                return new JsonResponse(
                    json_decode(
                        $serializer->serialize($file, 'json'),
                        true
                    ),
                    Response::HTTP_CREATED
                );
            }

            return new JsonResponse($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse('File was not set.', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param Request $request
     * @return null|Response
     */
    public function cgetAction(Request $request): ?Response
    {
        $repo = $this->getDoctrine()
            ->getRepository('RestUploaderBundle:File');

        $offset = (int)$request->get('page') * $repo->getItemsPerPage();

        $files = $repo->all($offset);

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [$encoder]);

                return new JsonResponse(
                    json_decode(
                        $serializer->serialize($files, 'json'),
                        true
                    ),
                    Response::HTTP_CREATED
                );
    }

    /**
     * @param File $file
     * @return null|Response
     */
    public function getAction(File $file): ?Response
    {
        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [$encoder]);

                return new JsonResponse(
                    json_decode(
                        $serializer->serialize($file, 'json'),
                        true
                    ),
                    Response::HTTP_CREATED
                );
    }

    /**
     * @param File $file
     * @return Response
     */
    public function downloadAction(File $file): Response
    {
        $manager = $this->get('rest_uploader.manager');

        return $manager->download($file);
    }

    /**
     * @param File $file
     * @return null|Response
     */
    public function deleteAction(File $file): ?Response
    {
        $manager = $this->get('rest_uploader.manager');

        if (!$manager->remove($file)) {
            return new JsonResponse('Failed to remove file.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $doctrine = $this->getDoctrine();

        $em = $doctrine->getManager();
        $em->remove($file);
        $em->flush();

        return new JsonResponse([], Response::HTTP_OK);
    }
}