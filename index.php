<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Euro Truck Simulator Save Editor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #282c34;
            color: white;
        }

        .container {
            background-color: #373f4b;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            max-width: 500px;
            width: 80%;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            cursor: pointer;
            background-color: #007bff;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        label:hover {
            background-color: #0056b3;
        }

        input[type="file"] {
            display: none;
        }

        p {
            font-size: 14px;
            margin-top: 20px;
            line-height: 1.5;
        }

        code {
            background-color: #21252b;
            padding: 5px;
            border-radius: 3px;
        }

        /* Your existing styles */
        #closeLoadedFile {
            background-color: red;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Text box styles */
        .dataLabel {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            text-align: left;
            width: calc(100% - 20px);
            margin-bottom: 5px;
            float: left;
        }

        .dataValue {
            margin-bottom: 20px;
        }

        .editableInput {
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 10px;
            width: calc(100% - 20px);
            float: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Euro Truck Simulator Level & Money Changer</h1>
       
        <div>
            <label for="saveFileInput">Upload File</label>
            <input type="file" accept=".sii" id="saveFileInput"/>
            <button id="closeLoadedFile" style="display: none;">Close Loaded File</button>
            <button id="saveChanges" style="display: none;">Save Changes</button>
        </div>
        <p id="saveDataLocation">On Windows, save data can be located at<br><code>documents\Euro Truck Simulator 2\profiles(Profile ID)\save\autosave</code></p>
        <div id="labelsAndValues" class="dataTextBox"></div>
    </div>

    <script>
        const saveFileInput = document.getElementById('saveFileInput');
        const closeLoadedFile = document.getElementById('closeLoadedFile');
        const saveChanges = document.getElementById('saveChanges');
        let fileContent = null;

        saveFileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    fileContent = e.target.result;
                    displayFileContents(fileContent);
                };
                reader.readAsText(file);
            }
        });

        closeLoadedFile.addEventListener('click', function() {
            window.location.reload();
        });

        saveChanges.addEventListener('click', function() {
    const editableInputs = document.querySelectorAll('.editableInput');
    let updatedContent = fileContent; // Başlangıçta dosyanın içeriğini al

    editableInputs.forEach(input => {
        const label = input.dataset.label;
        const regex = new RegExp(`${label}: ${input.defaultValue}`, 'g');
        updatedContent = updatedContent.replace(regex, `${label}: ${input.value}`);
    });

    downloadFile(updatedContent);
});

function downloadFile(content) {
    const updatedFile = new Blob([content], { type: 'text/plain' });
    const downloadLink = document.createElement('a');
    downloadLink.download = 'updated_file.sii';
    downloadLink.href = window.URL.createObjectURL(updatedFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}


        function displayFileContents(content) {
            const lines = content.split('\n');
            let isDriverAI = false;
            let labelsAndValuesHTML = '';

            lines.forEach(line => {
                let label = '';
                let value = '';

                if (line.includes('driver_ai :')) {
                    isDriverAI = true;
                } else if (!line.includes('bus_experience_points:') && (line.includes('experience_points:') && !isDriverAI && !line.includes('{') && !line.includes('}'))) {
                    const [key, val] = line.split(':');
                    label = key.trim();
                    value = `<input class="editableInput" data-label="${label}" type="text" value="${val.trim()}">`;
                } else if (!line.includes('bus_money_account:') && line.includes('money_account:')) {
                    const [key, val] = line.split(':');
                    label = key.trim();
                    value = `<input class="editableInput" data-label="${label}" type="text" value="${val.trim()}">`;
                } else if (line.includes('}')) {
                    isDriverAI = false;
                }

                if (label && value) {
                    labelsAndValuesHTML += `<div class="dataLabel">${label}</div><div class="dataValue">${value}</div><div style="clear:both;"></div>`;
                }
            });

            const labelsAndValuesContainer = document.getElementById('labelsAndValues');
            labelsAndValuesContainer.innerHTML = labelsAndValuesHTML;

            document.getElementById('saveDataLocation').style.display = 'none';
            saveFileInput.style.width = '150px';
            closeLoadedFile.style.display = 'inline-block';
            closeLoadedFile.style.marginLeft = '10px';
          

            saveChanges.style.display = 'inline-block';
            saveChanges.style.marginLeft = '10px';
        }
    </script>
</body>
</html>