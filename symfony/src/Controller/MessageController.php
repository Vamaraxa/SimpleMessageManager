<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Model\Message;
use App\Service\MessageHandler;
use Symfony\Component\HttpFoundation\Response;

#[Route('/', name: 'api_')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'messages_index', methods:['get'] )]
    public function index(Request $request, MessageHandler $fileHandler): JsonResponse
    {
        $sortBy = $request->query->get('sortBy');
        $orderBy = $request->query->get('orderBy', 'asc');

        if ($sortBy && !in_array($sortBy, ['uuid', 'dateOfCreated'])) {
            $data = [
                'status' => Response::HTTP_BAD_REQUEST,
                'error' => 'Bad request',
                'message' => [
                    'Available sort value' => [
                        'uuid', 'dateOfCreated'
                    ]
                ]
            ];
            return $this->json($data, Response::HTTP_BAD_REQUEST);
        }

        if ($orderBy && !in_array($orderBy, ['asc', 'desc'])) {
            $data = [
                'status' => Response::HTTP_BAD_REQUEST,
                'error' => 'Bad request',
                'message' => [
                    'Available order value' => [
                        'asc', 'desc'
                    ]
                ]
            ];
            return $this->json($data, Response::HTTP_BAD_REQUEST);
        }

        try {
            return $this->json($fileHandler->list($sortBy, $orderBy), Response::HTTP_OK);
        } catch (\Exception) {
            $data = [
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Internal Server Error'
            ];
            return $this->json($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/', name: 'messages_create', methods:['post'] )]
    public function create(Request $request, MessageHandler $fileHandler): JsonResponse
    {
        $message = new Message();
        $message->setText($request->request->get('text'));

        try {
            $fileHandler->create($message);
            return $this->json($message->getUuid(), Response::HTTP_OK);
        } catch (\Exception) {
            return $this->json('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{uuid}', name: 'messages_show', methods:['get'] )]
    public function show(string $uuid, MessageHandler $fileHandler): JsonResponse
    {
        $message = new Message();
        $message->setUuid($uuid);
        try {
            if ($fileHandler->isFile($message)) {
                return $this->json($fileHandler->show($message), Response::HTTP_OK);
            } else {
                return $this->json(
                    [
                        'status' => Response::HTTP_NOT_FOUND,
                        'errors' => "Not found"
                    ]
                    , Response::HTTP_NOT_FOUND
                );
            }
        } catch (\Exception) {
            return $this->json('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{uuid}', name: 'messages_update', methods:['put', 'patch'] )]
    public function update(Request $request, string $uuid, MessageHandler $fileHandler): JsonResponse
    {
        $text = $request->request->get('text');
        if (!isset($text)) {
            return $this->json(
                [
                    'status' => Response::HTTP_BAD_REQUEST,
                    'errors' => "Bad request",
                    'message' => 'Text field is required'
                ]
                , Response::HTTP_BAD_REQUEST
            );
        }

        $message = new Message();
        $message->setUuid($uuid);
        $message->setText($text);

        try {
            if ($fileHandler->isFile($message)) {
                $fileHandler->update($message);
                return $this->json($fileHandler->show($message), Response::HTTP_OK);
            } else {
                return $this->json('', Response::HTTP_NO_CONTENT);
            }
        } catch (\Exception) {
            return $this->json('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{uuid}', name: 'messages_delete', methods:['delete'] )]
    public function delete(string $uuid, MessageHandler $fileHandler): JsonResponse
    {
        $message = new Message();
        $message->setUuid($uuid);

        try {
            $fileHandler->delete($message);
            return $this->json('', Response::HTTP_NO_CONTENT);
        } catch (\Exception) {
            return $this->json('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}