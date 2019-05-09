<?php
/**----------------------------------------------------------------------------------
* Microsoft Developer & Platform Evangelism
*
* Copyright (c) Microsoft Corporation. All rights reserved.
*
* THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND,
* EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE IMPLIED WARRANTIES
* OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
*----------------------------------------------------------------------------------
* The example companies, organizations, products, domain names,
* e-mail addresses, logos, people, places, and events depicted
* herein are fictitious.  No association with any real company,
* organization, product, domain name, email address, logo, person,
* places, or events is intended or should be inferred.
*----------------------------------------------------------------------------------
**/
/** -------------------------------------------------------------
# Azure Storage Blob Sample - Demonstrate how to use the Blob Storage service.
# Blob storage stores unstructured data such as text, binary data, documents or media files.
# Blobs can be accessed from anywhere in the world via HTTP or HTTPS.
#
# Documentation References:
#  - Associated Article - https://docs.microsoft.com/en-us/azure/storage/blobs/storage-quickstart-blobs-php
#  - What is a Storage Account - http://azure.microsoft.com/en-us/documentation/articles/storage-whatis-account/
#  - Getting Started with Blobs - https://azure.microsoft.com/en-us/documentation/articles/storage-php-how-to-use-blobs/
#  - Blob Service Concepts - http://msdn.microsoft.com/en-us/library/dd179376.aspx
#  - Blob Service REST API - http://msdn.microsoft.com/en-us/library/dd135733.aspx
#  - Blob Service PHP API - https://github.com/Azure/azure-storage-php
#  - Storage Emulator - http://azure.microsoft.com/en-us/documentation/articles/storage-use-emulator/
#
**/
require_once 'vendor/autoload.php';
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=azurestoragedicoding;AccountKey=db80qROD8xhhnhynAuqO/tAJfu58mURdhkAbDvMwx8p1kieBOOf/lnPJ+B4BwTpVryrmM3HcMsyu4tfjfhUO8A==;EndpointSuffix=core.windows.net";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
$containerName = "blockblobsenno";
if (isset($_POST["Upload"])) {
    $fileToUpload = "Enno3.jpg";
    $blobURL = "";
    // Create container options object.
    $createContainerOptions = new CreateContainerOptions();

    $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
    // Set container metadata.
    $createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");

    try {
        // Create container.
        //$blobClient->createContainer($containerName, $createContainerOptions);
        // Getting local file so that we can upload it to Azure
        $myfile = fopen($fileToUpload, "r") or die("Unable to open file!");
        fclose($myfile);

        # Upload file as a block blob
        echo "Uploading BlockBlob: ".PHP_EOL;
        echo $fileToUpload;
        echo "<br />";

        $content = fopen($fileToUpload, "r");
        try {
            //Upload blob
            $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
            echo "File has successfully uploaded";
        } catch(ServiceException $e){
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
        }
    }
    catch(ServiceException $e){
          // Handle exception based on error codes and messages.
          // Error codes and messages are here:
          // http://msdn.microsoft.com/library/azure/dd179439.aspx
          $code = $e->getCode();
          $error_message = $e->getMessage();
          echo $code.": ".$error_message."<br />";
    }
    catch(InvalidArgumentTypeException $e){
          // Handle exception based on error codes and messages.
          // Error codes and messages are here:
          // http://msdn.microsoft.com/library/azure/dd179439.aspx
          $code = $e->getCode();
          $error_message = $e->getMessage();
          echo $code.": ".$error_message."<br />";
    }
}
if(isset($_POST["Delete"])) {
    try{
        // Delete container.
        echo "Deleting Container".PHP_EOL;
        echo $_GET["containerName"].PHP_EOL;
        echo "<br />";
        $blobClient->deleteContainer($_GET["containerName"]);
    }
    catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Analyze Sample</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body>

<script type="text/javascript">
    function processImage() {
        var subscriptionKey = "01291cc67f0648e695e9c71e6250b0f3";

        var uriBase =
            "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
            // "https://southeastasia.api.cognitive.microsoft.com/";

        // Request parameters.
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "language": "en",
        };

        // Display the image.
        var sourceImageUrl = document.getElementById("inputImage").value;
        document.querySelector("#sourceImage").src = sourceImageUrl;

        // Make the REST API call.
        $.ajax({
            url: uriBase + "?" + $.param(params),

            // Request headers.
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },

            type: "POST",

            // Request body.
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })

        .done(function(data) {
            // Show formatted JSON on webpage.
            $("#responseTextArea").val(JSON.stringify(data, null, 2));
        })

        .fail(function(jqXHR, textStatus, errorThrown) {
            // Display error message.
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
    };
</script>

<h1>Final Submission MACD</h1>
<form method="post" action="index.php">
  <div class="input-group">
    <button type="submit" class="btn" name="Upload">Upload</button>
    <button type="submit" class="btn" name="load_data">Load Data</button>
  </div>
</form>
<?php
if(isset($_POST["load_data"])){
    try {    // List blobs.
        $listBlobsOptions = new ListBlobsOptions();
        $listBlobsOptions->setPrefix("Enno");
        echo "<br />These are the blobs present in the container: <br />";
        do{
            $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
            foreach ($result->getBlobs() as $blob)
            {
                echo $blob->getName().": ".$blob->getUrl()."<input type='text' name='inputImage' id='inputImage' value='".$blob->getUrl()."' hidden/>
                <button onclick='processImage()'>Start Analyzing</button><br />";
            }

            $listBlobsOptions->setContinuationToken($result->getContinuationToken());
        } while($result->getContinuationToken());
        echo "<br />";
    }
    catch(ServiceException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
    catch(InvalidArgumentTypeException $e){
        // Handle exception based on error codes and messages.
        // Error codes and messages are here:
        // http://msdn.microsoft.com/library/azure/dd179439.aspx
        $code = $e->getCode();
        $error_message = $e->getMessage();
        echo $code.": ".$error_message."<br />";
    }
}
?>
<br><br>
<div id="wrapper" style="width:1020px; display:table;">
    <div id="jsonOutput" style="width:600px; display:table-cell;">
        Response:
        <br><br>
        <textarea id="responseTextArea" class="UIInput"
                  style="width:580px; height:400px;"></textarea>
    </div>
    <div id="imageDiv" style="width:420px; display:table-cell;">
        Source image:
        <br><br>
        <img id="sourceImage" width="400" />
    </div>
</div>
</body>
</html>
