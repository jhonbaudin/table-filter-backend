<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Tasks;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TasksController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/v1/tasks",
     *     summary="List all tasks with database pagination.",
     *     description="All tasks",
     *     tags={"Tasks API"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Number of page (by default the main page is number 1).",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Set the limit of elements per page (by default are 10 elements per page).",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         required=false,
     *         description="To search by fulltext on name column.",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="completed",
     *         in="query",
     *         required=false,
     *         description="To filter by status completed(1) or incompleted(0)",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Response is OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="Method not allowed"
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $query = $request->query->all();
        $limit = array_key_exists('limit', $query) && $query['limit'] ? (int) $query['limit'] : 10;
        $page = array_key_exists('page', $query) && $query['page'] ? (int) $query['page'] : 1;
        $search = array_key_exists('search', $query) && $query['search'] ? $query['search'] : null;
        $status = array_key_exists('completed', $query) && $query['completed'] ? $query['completed'] : null;
        $offset = ($limit * $page) - $limit;

        $tasks = Tasks::select();

        if ($search) {
            $tasks->whereRaw('MATCH(name) AGAINST(?)', [$search]);
        }

        if ($status) {
            $tasks->where('completed', $status);
        }
        $total = $tasks->get()->count();

        $result = new LengthAwarePaginator($tasks->skip($offset)->take($limit)->orderBy('id', 'desc')->get(), $total, $limit, $page);

        return response()->json(collect($result), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tasks/{taskId}",
     *     summary="Show an specific task.",
     *     description="Selected task",
     *     tags={"Tasks API"},
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="Task id to search by.",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Response is OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not Found",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="Method not allowed"
     *     ),
     * )
     */
    public function get($id)
    {
        $task = Tasks::find($id);
        return response()->json($task, $task ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tasks",
     *     summary="Create a task.",
     *     description="Create a task",
     *     tags={"Tasks API"},
     *     @OA\RequestBody(
     *         required=true,
     *         request="Task",
     *         description="Task information to create.",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="name of task"),
     *             @OA\Property(property="completed", type="boolean", example="true"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad request",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="Method not allowed"
     *     ),
     * )
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:App\Models\Tasks',
            'completed' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response(['message' => 'The given data is invalid.', 'errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $task = Tasks::create([
            'name' => (string) $request->name,
            'completed' => $request->completed ?: false,
        ]);

        return response()->json(
            ["message" => "Task was created successfully!", "task" => $task],
            Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/tasks/{taskId}",
     *     summary="Update a task.",
     *     description="Update a task",
     *     tags={"Tasks API"},
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="Task id to update by.",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         request="Task",
     *         description="Task information to update.",
     *         @OA\JsonContent(
     *             required={"name","completed"},
     *             @OA\Property(property="name", type="string", example="update the selected task"),
     *             @OA\Property(property="completed", type="boolean", example="true"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad request",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not Found",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="Method not allowed"
     *     ),
     * )
     */
    public function update($id, Request $request)
    {

        $task = Tasks::find($id);

        if (!$task) {
            return response(['message' => 'The given data is invalid, we can not find the task.'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:App\Models\Tasks,name,' . $task->id,
            'completed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response(['message' => 'The given data was invalid.', 'errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $task->update([
            'name' => (string) $request->name,
            'completed' => $request->completed,
        ]);

        return response()->json(
            ["message" => "Task was updated successfully!", "task" => $task],
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/tasks/{taskId}",
     *     summary="Delete an specific task.",
     *     description="Selected task",
     *     tags={"Tasks API"},
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         description="Task id to delete by.",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Response is OK",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not Found",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response="405",
     *         description="Method not allowed"
     *     ),
     * )
     */
    public function delete($id)
    {
        $task = Tasks::find($id);

        if (!$task) {
            return response(['message' => 'The given data is invalid, we can not find the task.'], Response::HTTP_NOT_FOUND);
        }

        $task->delete();
        return response(['message' => 'Task was deleted successfully!'], Response::HTTP_OK);
    }
}
