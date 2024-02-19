<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RCAuth;

use App\Models\User;
use App\Models\OldFormBuilder\OldFormBuilderForms;
use App\Models\OldFormBuilder\OldFormBuilderPermissions;

class TypeaheadController extends Controller
{

    public function user_search(Request $request)
    {
        $request->validate(["search" => "required"]);
        $search_terms = $request->input("search");

        if(strlen($search_terms) < 3) {
          return response()->json([]);
        }
        $search_terms = explode(' ', $search_terms);

        $students = User::where(function ($query) {
                        $query->where('Faculty', 'Yes')
                               ->orWhere('Staff', 'Yes')
                               ->orWhere("Student", 'Yes');
                        })
        ->where(function ($query) use ($search_terms) {
          foreach($search_terms as $term) {
            $query->Where(function ($search_query) use ($term) {
              $search_query->where("FirstName", "LIKE", sprintf("%%%s%%", $term))
                           ->orWhere("LastName", "LIKE", sprintf("%%%s%%", $term))
                           ->orWhere("MiddleName", "LIKE", sprintf("%%%s%%", $term))
                           ->orWhere("nick_name", "LIKE", sprintf("%%%s%%", $term))
                           ->orWhere("NickName", "LIKE", sprintf("%%%s%%", $term));
            });
          }
        })->get();

        $response = [];

        foreach($students as $student) {
          $response_entry                 = [];
          $response_entry['id']           = $student->RCID;
          $response_entry['display_data'] = view()->make("typeahead.typeahead", ['person' => $student])->render();
          $response_entry['input_data']   = $student->natural_name;//Because that's what it was in the old typeahead
          $response[]                     = $response_entry;
        }

        return ["data" => $response];
    }
}
