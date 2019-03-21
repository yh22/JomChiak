<?php


namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\BaseController as BaseController;
use Validator;
use Carbon\Carbon;

use App\User;
use App\UsersInfo;
use App\ParkingArea;
use App\ParkingCode;
use App\Track;

class UserController extends BaseController
{
	public function getUserInfo()
    {
        $userId=$this->getUserId();
        $user=User::findOrFail($userId);
        $username=$user->name;

        $userInfo=UsersInfo::where('user_id',$userId)->first();
        $userCredit=$userInfo->credit;

        $success['username']=$username;
        $success['userCredit']=$userCredit;

        return $this->sendResponse($success,"User information retreive successfully");
    }

    public function getParkingArea()
    {
        $parking=ParkingArea::all();

        $count=0;
        $success=array();
        foreach($parking as $parkingArea)
        {
            $parkingInfo = [
            'id' => $parkingArea->id,
            'name'    => $parkingArea->name,
            'coordinate' => [
                                'longitude' => floatval($parkingArea->longitude),
                                'latitude' => floatval($parkingArea->latitude),
                            ],
            'space' => $parkingArea->space,
            'space_left' => $parkingArea->space_left,
            ];
            array_push($success, $parkingInfo);
        }

        return $this->sendResponse($success,"All parking area retreive successfully");
    }

    public function scan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parking_id' => 'required',
            'timestamp' => 'required',
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors()); 
        }

        $input = $request->all();
        $userId=$this->getUserId();

        $checkEntry=Track::where('parking_id',$input['parking_id'])
            ->where('user_id',$userId)
            ->where('confirm',true)
            ->first();

        $userInfo=UsersInfo::where('user_id',$userId)
                ->first();
        $userCurrentCredit=$userInfo->credit;

        if(empty($checkEntry))
        {
            //update enter time
            $input['user_id']=$userId;
            $input['parking_id']=$input['parking_id'];
            $input['enter']=$input['timestamp'];
            $input['confirm']=true;
            $track=Track::create($input);

            //update parking space upon enter
            $parkingSpace=ParkingArea::where('id',$input['parking_id'])
                ->first();
            $newParkingSpace=$parkingSpace->space_left-1;
            ParkingArea::where('id',$input['parking_id'])
                ->update(['space_left'=>$newParkingSpace]);

            $success['time']=Carbon::createFromTimestamp($input['timestamp'])->toDateTimeString(); 
            return $this->sendResponse($success,"Welcome");
        }
        else
        {

            $track=Track::where('parking_id',$input['parking_id'])
                ->where('user_id',$userId)
                ->where('confirm',true)
                ->first();

            $enterTime=$track->enter;

            //update exit time
            Track::where('parking_id',$input['parking_id'])
                ->where('user_id',$userId)
                ->where('confirm',true)
                ->update(['exit'=>$input['timestamp']]);

            Track::where('parking_id',$input['parking_id'])
                ->where('user_id',$userId)
                ->where('confirm',true)
                ->update(['confirm'=>false]);

            //update parking space upon exit
            $parkingSpace=ParkingArea::where('id',$input['parking_id'])
                ->first();
            $newParkingSpace=$parkingSpace->space_left+1;
            ParkingArea::where('id',$input['parking_id'])
                ->update(['space_left'=>$newParkingSpace]);

            //calculate duration and payment
            $totalTimeEnter=$input['timestamp']-$enterTime;

            $parkingInfo=ParkingArea::where('id',$input['parking_id'])
                ->first();

            $fees=0;
            //weekend
            if(Carbon::createFromTimestamp($input['timestamp'])->isWeekend())
            {
                if($totalTimeEnter>$parkingInfo->free_time)
                {
                    if($totalTimeEnter<=3600)
                    {
                        $fees=$parkingInfo->weekend_first;
                    }
                    else
                    {
                        $firstHour=$parkingInfo->weekend_first;
                        $numSubHour=ceil(($totalTimeEnter-3600)/3600);
                        $subHour=$parkingInfo->weekend*$numSubHour;
                        $fees=$firstHour+$subHour;
                    }
                }
            }
            //weekday
            else
            {
               if($totalTimeEnter>$parkingInfo->free_time)
                {
                    if($totalTimeEnter<=3600)
                    {
                        $fees=$parkingInfo->weekend_first;
                    }
                    else
                    {
                        $firstHour=$parkingInfo->weekday_first;
                        $numSubHour=ceil(($totalTimeEnter-3600)/3600);
                        $subHour=$parkingInfo->weekday*$numSubHour;
                        $fees=$firstHour+$subHour;
                    }
                } 
            }

            $userInfo=UsersInfo::where('user_id',$userId)
                ->first();

            $userCurrentCredit=$userInfo->credit;

            $userNewCredit=$userCurrentCredit;

            $success['time']=Carbon::createFromTimestamp($input['timestamp'])->toDateTimeString();
            $success['duration']=$totalTimeEnter;
            $success['fees']=$fees;
            return $this->sendResponse($success,"Goodbye");
        }
    }
}