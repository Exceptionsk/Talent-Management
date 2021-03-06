<?php

namespace App\Http\ApiControllers;

use Illuminate\Http\Request;
use App\Http\ApiControllers\APIBaseController as BaseController;
use App\Repositories\Topic\TopicRepositoryInterface as TopicInterface;
use App\Http\Resources\Topic as TopicResource;
use App\Events\ContentCRUDEvent;
use Validator;

class TopicController extends BaseController
{
    public $topicInterface;

    public function __construct(Request $request, TopicInterface $topicInterface)
    {
        $this->topicInterface = $topicInterface;
        $this->method           = $request->getMethod();
        $this->endpoint         = $request->path();
        $this->startTime        = microtime(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->offset = isset($request->offset)? $request->offset : 0;
        $this->limit  = isset($request->limit)? $request->limit : 30;
        $topic  = TopicResource::collection($this->topicInterface->getAll($this->offset, $this->limit));
        $total = $this->topicInterface->total();
        $this->data($topic);
        $this->total($total);
        return $this->response('200');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                        'course_id'   =>  'required|exists:course,id',
                        'topic'       =>  'required',
                        'descriptions'=>  'required',
                        'start_date'  =>  'required|date',
                        'end_date'    =>  'required|date',
                        'admin_id'    =>  'required'
                    ]);

        if ($validator->fails()) {
            $this->setError('400');
            $messages=[];

            foreach ($validator->messages()->toArray() as $key=>$value) {
                 $messages[] = (object)['attribue' => $key, 'message' => $value[0]];
            }

            $this->setValidationError(['validation' => $messages]);
            return $this->response('400');
        }

         $topic = $request->all();
         $result = $this->topicInterface->store($topic);

         if (isset($result)) {
           $this->data(array('id' =>  $result->id));
           event(new ContentCRUDEvent('Create Topic', $request->admin_id, 'Create', 'Created '. $result->name. ' Topic'));
         }

         return $this->response('201');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $topic = $this->topicInterface->find($id);
        if (empty($topic)) {
            $this->setError('404', $id);
            return $this->response('404');
        }else{
            $topic = new TopicResource($topic);
            $this->data(array($topic));
            return $this->response('201');
        }
    }

    public function getByCourseId($course_id)
    {
        $topic = $this->topicInterface->getByCourseId($course_id);
        if (empty($topic)) {
            $this->setError('404', $id);
            return $this->response('404');
        }else{
            $topic =  TopicResource::collection($topic);
            $this->data($topic);
            return $this->response('201');
        }
    }

    public function list($course_id)
    {
        $total = $this->topicInterface->total();
        $this->total($total);
        $topic = $this->topicInterface->getList($course_id);
        if (empty($topic)) {
            $this->setError('404', $id);
            return $this->response('404');
        }else{
            $this->data($topic);
            return $this->response('201');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
                        'start_date'  =>  'date',
                        'end_date'    =>  'date',
                        'admin_id'    =>  'required'
                    ]);
        if ($validator->fails()) {
            $this->setError('400');
            $messages=[];

            foreach ($validator->messages()->toArray() as $key=>$value) {
                 $messages[] = (object)['attribue' => $key, 'message' => $value[0]];
            }

            $this->setValidationError(['validation' => $messages]);
            return $this->response('400');
        }
        $topic = $this->topicInterface->find($id);
        if (empty($topic)) {
            $this->setError('404', $id);
            return $this->response('404');
        }else{
            if ($this->topicInterface->update($request->all(),$id)) {
                $this->data(array('updated' =>  1));
                event(new ContentCRUDEvent('Update Topic', $request->admin_id, 'Update', 'Updated '. $topic->name. ' Topic'));
                return $this->response('200');
            }else {
                return $this->response('500');
            };
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
                'admin_id'  =>  'required'
            ]);

        if ($validator->fails()) {
            $this->setError('400');
            $messages=[];

            foreach ($validator->messages()->toArray() as $key=>$value) {
                  $messages[] = (object)['attribue' => $key, 'message' => $value[0]];
            }

            $this->setValidationError(['validation' => $messages]);
            return $this->response('400');
        }
        $topic = $this->topicInterface->find($id);
        if (empty($topic)) {
            $this->setError('404', $id);
            return $this->response('404');
        }else{
          $this->topicInterface->destroy($id);
          $this->data(array('deleted' =>  1));
          event(new ContentCRUDEvent('Delete Topic', $request->admin_id, 'Delete', 'Deleted '. $topic->topic. ' Topic'));
          return $this->response('200');
        }
    }
}
