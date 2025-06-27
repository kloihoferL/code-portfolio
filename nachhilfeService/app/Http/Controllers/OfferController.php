<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Offer;
use App\Models\Slot;
use App\Models\SubCourse;
use Database\Seeders\OfferTableSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OfferController extends Controller
{

    public function index(): JsonResponse
    {
        $offers = Offer::with(['course.subcourses', 'giver', 'slots'])->get();
        return response()->json($offers, 200);
    }

    public function getOfferByUserId($userId): JsonResponse
    {
        $offers = Offer::with(['course.subcourses', 'giver', 'slots'])
            ->where('user_id', $userId)
            ->get();
        return response()->json($offers, 200);
    }

    public function show($id): JsonResponse
    {
        $offer = Offer::with(['course.subcourses', 'giver', 'slots'])->find($id);
        if ($offer) {
            return response()->json($offer, 200);
        } else {
            return response()->json(['message' => 'Offer not found'], 404);
        }
    }

    public function delete($id): JsonResponse
    {
        //$offer = Offer::find($id);
        $offer = Offer::where('id', $id)->first();

        if ($offer) {
            //checken ob er das löschen darf
            //if (Gate::denies('own-offer', $offer)) {
                //return response()->json(['message' => 'You do not own this offer.'], 403);
            //}else{
                $offer->delete();
                return response()->json(['message' => 'Offer deleted successfully'], 200);
            //}
        } else {
            return response()->json(['message' => 'Offer not found'], 404);
        }
    }

    public function save(Request $request): JsonResponse
    {
        $request = $this->parseRequest($request);

        DB::beginTransaction();
        try {
            $course = null;
            if (isset($request->course)) {
                $course = new Course();
                $course->name = $request->course['name'];
                $course->save();

                if (isset($request->course['subcourses']) && is_array($request->course['subcourses'])) {
                    foreach ($request->course['subcourses'] as $sub) {
                        $subcourse = new Subcourse();
                        $subcourse->name = $sub['name'];
                        $subcourse->course_id = $course->id;
                        $subcourse->save();
                    }
                }
            }

            $offer = Offer::create([
                'name' => $request->name,
                'description' => $request->description,
                'comment' => $request->comment,
                'user_id' => $request->user_id,
                'course_id' => $course?->id
            ]);

            if (isset($request->slots) && is_array($request->slots)) {
                foreach ($request->slots as $slotData) {
                    $slot = new Slot();
                    $slot->start_time = $slotData['start_time'];
                    $slot->end_time = $slotData['end_time'];
                    $offer->slots()->save($slot);
                }
            }

            DB::commit();
            return response()->json($offer, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Saving offer failed: ' . $e->getMessage()], 500);
        }
    }





    /*public function update(Request $request, string $id): JsonResponse
    {
        $request = $this->parseRequest($request); // optional, je nach Umsetzung

        DB::beginTransaction();

        try {
            $offer = Offer::find($id);
            if (Gate::denies('own-offer', $offer)) {
                return response()->json(['message' => 'You do not own this offer.'], 403);
            }else{
                if (!$offer) {
                    return response()->json(['message' => "Offer with ID $id not found"], 404);
                }

                $offer->update($request->all());
                DB::commit();

                return response()->json($offer, 200);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Updating offer failed: ' . $e->getMessage()], 500);
        }
    }*/

    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $offer = Offer::find($id);
            if (!$offer) {
                return response()->json(['message' => "Offer with ID $id not found"], 404);
            }

            if (Gate::denies('own-offer', $offer)) {
                return response()->json(['message' => 'You do not own this offer.'], 403);
            }

            $offer->update([
                'name' => $request->name,
                'description' => $request->description,
                'comment' => $request->comment,
                'booked' => $request->booked,
            ]);

            if ($request->has('course')) {
                $courseData = $request->input('course');
                $course = $offer->course;
                $course->update([
                    'name' => $courseData['name'],
                ]);

                // Subcourses aktualisieren
                $course->subcourses()->delete(); // alte löschen
                foreach ($courseData['subcourses'] as $sub) {
                    $course->subcourses()->create([
                        'name' => $sub['name'],
                    ]);
                }
            }

            // Slots aktualisieren
            $offer->slots()->delete();
            foreach ($request->input('slots', []) as $slot) {
                $offer->slots()->create([
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                ]);
            }

            DB::commit();

            return response()->json($offer->fresh(), 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Updating offer failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeBookedStatus($id): JsonResponse
    {
        $offer = Offer::find($id);
        if ($offer) {
            $offer->booked = !$offer->booked;
            $offer->save();
            return response()->json($offer, 200);
        } else {
            return response()->json(['message' => 'Offer not found'], 404);
        }
    }



    private function parseRequest(Request $request): Request{
        $date = new \DateTime($request->published);
        $request['published'] = $date->format('Y-m-d H:i:s');
        return $request;
    }

}
