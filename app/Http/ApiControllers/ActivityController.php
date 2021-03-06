<?php

namespace App\Http\ApiControllers;

use Illuminate\Http\Request;
use App\Http\ApiControllers\APIBaseController as BaseController;
use App\Repositories\Activity\ActivityRepositoryInterface as ActivityInterface;
use App\Http\Resources\Activity as ActivityResource;
use App\Events\ContentCRUDEvent;
use Validator;

class ActivityController extends BaseController
{

    public $activityInterface;

    public function __construct(Request $request, ActivityInterface $activityInterface)
    {
        $this->activityInterface = $activityInterface;
        $this->method            = $request->getMethod();
        $this->endpoint          = $request->path();
        $this->startTime         = microtime(true);
    }

    private function convertPostType($value, $default)
    {
        if (strcasecmp($value, 'post') == 0) {
            return 0;
        }elseif (strcasecmp($value, 'announcement') == 0){
            return 1;

        }else{

          return $default;
        }
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
         $name         = isset($request->name)? $request->name : '%';
         $speaker      = isset($request->speaker)? $request->speaker : '%';
         $type='';
         if (!isset($request->type)) {
            $type="%";
            $total = $this->activityInterface->total(3);
         }else{
           $type = $this->convertPostType($request->type, '%');
           $total = $this->activityInterface->total($type);
         }
         // dd($type);
         $activity = ActivityResource::collection($this->activityInterface->getAll($this->offset, $this->limit, $name, $speaker, $type));
         $total = $this->activityInterface->total($type);
         $this->total($total);
         $this->data($activity);
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
              'name'          =>  'required',
              'date'          =>  'required|date',
              'descriptions'  =>  'required',
              'speaker_name'  =>  'required',
              'image'         =>  'required|image',
              'type'          =>  'required',
              'admin_id'      =>  'required|exists:admin,id'
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

       $activity = $request->all();
       $path = $request->file('image')->getRealPath();
       $img = base64_encode(file_get_contents($path));
       $activity['image'] = $img;
       $activity['type'] = $this->convertPostType($activity['type'], 0);
       $result = $this->activityInterface->store($activity);

       if (isset($result)) {
          event(new ContentCRUDEvent('Create Activity', $request->admin_id, 'Create', 'Made '. $result->name. ' Activity'));
          $this->data(array('id' =>  $result));
          return $this->response('201');
       }else{
          return $this->response('500');
       }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $activity = $this->activityInterface->find($id);
        if (empty($activity)) {
            $this->setError('404', $id);
            return $this->response('404');
        }else{
            $activity = new ActivityResource($activity);
            $this->data(array($activity));
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
              'date'          =>  'date',
              'image'         =>  'image',
              'admin_id'      =>  'required|exists:admin,id'
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

        $activity = $this->activityInterface->find($id);
        if (empty($activity)) {
            $this->setError('404', $id);
            return $this->response('404');
        }else{
          $activity = $request->all();
          if (isset($request->image)) {
              $path = $request->file('image')->getRealPath();
              $img = base64_encode(file_get_contents($path));
              $activity['image'] = $img;
          }
          if (isset($request->type)) {
             $activity['type'] = $this->convertPostType($request->type,0);
          }
            if ($this->activityInterface->update($activity,$id)) {
                event(new ContentCRUDEvent('Update Activity', $request->admin_id, 'Update', 'Updated '. $activity['name']. ' Activity'));
                $this->data(array('updated' =>  1));
                return $this->response('200');
            }else{
                return $this->response('200');
            }

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
        $activity = $this->activityInterface->find($id);
        if (empty($activity)) {
            $this->setError('404', $id);
            return $this->response('404');
        }else{
            $this->activityInterface->destroy($id);
            event(new ContentCRUDEvent('Delete Activity', $request->admin_id, 'Deleted', 'Deleted '. $activity->name. ' Activity'));
            $this->data(array('deleted' =>  1));
            return $this->response('200');
        }
    }

    /**
     * show image of the Activity resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function image($id)
    {
        $img = $this->activityInterface->find($id)->image;
        return response(base64_decode($img), 200, ['Content-Type' => 'image/png',]);
    }
}
