<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Login;
use App\Models\Album;
use App\Models\Edit;
use App\Models\View;
use App\Models\Comment;
use App\Models\Report;
use App\Models\Reminder;
use App\Http\Controllers\Controller;
use \Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $response)
    {
        //
        $username = $response->accountName;

        $pull_img = Account::where('username', $username)->get(['id', 'icon', 'comment']);

        return ['img_icon_data' => $pull_img];
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

        $account_content = Account::where('id', $id)->get(['username', 'icon', 'comment']);

        $name = $account_content[0]->username;
        $comment = $account_content[0]->comment;

        $number = $request->judgeNumber;

        //???????????????????????????????????????
        if($number == 1) {

            $name = $request->changeContent;

            $exist_name = Account::where('username', $name)->where('id', '<>', $id)->get();

            if(count($exist_name) != 0) {
                return ['judge_success' => false];
            }


        } else if($number == 2) {

            $comment = $request->changeContent;

        }

        $get_name = Account::where('id', $id)->get('username');

        Account::where('id', $id)
                    ->update([
                        'username' => $name,   
                        'comment' => $comment,
                    ]);
                    
        $sql_name = [new Login(), new Album(), new Edit(), new View(), new Comment(), new Report(), new Reminder()];
        
        foreach($sql_name as $sql) {

            $sql->where('username', $get_name[0]->username)
                ->update([
                    'username' => $name,
                ]);

        }

        return ['judge_success' => true];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function img_post(Request $response)
    {

        $file_name = $response->file->getClientOriginalName();
        $userId = $response->userId;
        $storage = Storage::disk('s3');

        //??????????????????????????????
        $get_before_icon = Account::where('id', $userId)->get('icon');

        $storage->delete($get_before_icon[0]->icon);

        //?????????????????????
        $post_image = $storage->put('account', $response->file, 'public');

        Account::where('id', $userId)
                            ->update([
                                'icon' => $post_image,
                            ]);

        return ['judge_success' => true];
    }
}
