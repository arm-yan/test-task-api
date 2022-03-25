<?php

namespace Api\Controller;

use App\Model;
use App\Storage\DataStorage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController 
{
    /**
     * @var DataStorage
     */
    private $storage;

    public function __construct(DataStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param Request $request
     * 
     * @Route("/project/{id}", name="project", method="GET")
     *
     * @throws Model\NotFoundException
     * @throws Throwable
     */
    public function projectAction(Request $request)
    {
        // We dont want to use try here, and throw exceptions
        // Need to create custom Exception handler, that will wrap all the exceptions into new Response()
        try {
            $project = $this->storage->getProjectById($request->get('id'));

            return new Response($project->toJson());
        } catch (Model\NotFoundException $e) {
            return new Response('Not found', 404);
        } catch (\Throwable $e) {
            return new Response('Something went wrong', 500);
        }
    }

    /**
     * @param Request $request
     *
     * @Route("/project/{id}/tasks", name="project-tasks", method="GET")
     */
    public function projectTaskPagerAction(Request $request)
    {
        // It would be good idea to use DTOs, like getTasksDto() with getters and setters, validation rules
        $tasks = $this->storage->getTasksByProjectId(
            $request->get('id'),
            $request->get('limit'),
            $request->get('offset')
        );

        // In Project model we have method toJson(), so it would be good to add it for all models,
        // using some ModelInterface, where will be declared toJson() method
        return new Response(json_encode($tasks));
    }

    /**
     * @param Request $request
     *
     * @Route("/project/{id}/tasks", name="project-create-task", method="PUT")
     */
    public function projectCreateTaskAction(Request $request)
    {
		$project = $this->storage->getProjectById($request->get('id'));
		// Need to create custom Exception handler, that will wrap all the exceptions into new Response()
        // In case we want to throw exception for null result,
        // it will be good to have method in storage like getProjectByIdOrFail()
        // and getProjectById() will return Project or null
        if (!$project) {
            //Why we have different type of responses? Need to unify, always use Response, or JsonResponse,
            // or something else
			return new JsonResponse(['error' => 'Not found']);
		}

        // We dont want to use $_REQUEST, we use Request $request, but, it would be good to use DTOs,
        // like createTaskDto(), with its getters and setters, and validation rules
		return new JsonResponse(
			$this->storage->createTask($_REQUEST, $project->getId())
		);
    }
}
