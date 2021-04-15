<?php

namespace App\Controller;

use App\Entity\Parcels;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class ParcelController extends AbstractController
{
    /**
     * @Route("/api/parcels/{id}", name="parcel_show")
     * @SWG\Get(
        *path="/api/parcels/{id}",
        *security={{"Bearer":{}}}, 
        *parameters={
            *@Swg\Parameter(name="id", in="path", description="Id parcel", type="integer")
        *},
        *@Swg\Response(
        *   response="200",
        *   description="Details parcel",
        *   schema=@Swg\Schema(type="object",ref="#/definitions/default")
        *)
        *)
     */
    public function show(Request $request)
    {
        $parcel=$this->getDoctrine()->getRepository(Parcels::class)->find($request->get("id"));
        if(!$parcel){
            return new JsonResponse(array("code"=>500,"status"=>false,"message"=>"invalid parcel"));
        }
        $data=array(
            "id"=>$parcel->getId(),
            "reference"=>$parcel->getReference(),
            "destinataire"=>$parcel->getDestinataireName(),
            "expediteur"=>$parcel->getExpediteurName(),
            "telephone"=>$parcel->getTelephone()
        );
        return new JsonResponse(array("code"=>200,"status"=>true,"data"=>$data));
    }
}
