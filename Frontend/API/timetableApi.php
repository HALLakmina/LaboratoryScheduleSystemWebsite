<?php
    namespace Frontend\API;
    use Exception;
    class TimetableApi {
        private $respond=[];
        public function __construct($request, $data=null){
            $this->callApi($request, $data=null);
        }
        private function callApi($request, $data=null){
            try{
                switch($request){
                    case "getTimetableData":
                        return $this->getTimetableData();
                    default:
                        break;
                }
            }
            catch(Exception $e){
                return $e;
            }
        }
        private function setRespond ($respond){
            $this->respond = $respond;
        }
        public function getRespond (){
            return $this->respond;
        }
        private function getTimetableData(){

            $apiUrl = "http://localhost/project01/LaboratoryScheduleSystemWebsite/Backend/api/v1/user";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $apiUrl);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // (Optional) If your API requires headers, you can set them like this:
            // $headers = [
            //     'Authorization: Bearer YOUR_ACCESS_TOKEN',
            // //    'Content-Type: application/json'
            // ];
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // 4. Execute the cURL request
            $response = curl_exec($ch);

            // 5. Check for cURL errors
            if (curl_errno($ch)) {
                echo 'cURL Error: ' . curl_error($ch);
            } else {
                // 6. Close the cURL session
                curl_close($ch);

                // 7. Process the API response
                // Assuming the API returns JSON, decode it
                $data = json_decode($response, true); // true for associative array, false for object
                $this-> setRespond($data);
            }
            
        } 
    } 
?>