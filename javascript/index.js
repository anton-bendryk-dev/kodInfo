let textPrList = document.querySelector("#text_pr_list");
let mailingType = document.querySelector("#mailing_type");
let createMailingTextBtn = document.querySelector("#createMailingTextBtn");
let calendar = document.querySelector(".calendar");
let numberOfSegments = document.querySelector("#numberOfSegments");
let containerForText = document.querySelector(".container-for-text");
// считываем файл json
function readTextFile(file, callback) {
    var rawFile = new XMLHttpRequest();
    rawFile.overrideMimeType("https://anton-bendryk-dev.github.io/kodInfo/javascript/projects.json");
    rawFile.open("GET", file, true);
    rawFile.onreadystatechange = function () {
        if (rawFile.readyState === 4 && rawFile.status == "200") {
            callback(rawFile.responseText);
        }
    }
    rawFile.send(null);
}

readTextFile("https://anton-bendryk-dev.github.io/kodInfo/javascript/projects.json", function (text) {
    var data = JSON.parse(text);
    // достаём названия проектов
    for (let i = 0; i < data.length; i++) {
        const option = document.createElement('option');
        option.textContent = data[i].name;
        textPrList.appendChild(option);
    }

    function deleteOption() {
        while (mailingType.firstChild) {
            mailingType.removeChild(mailingType.firstChild);
        };
        mailingType.innerHTML = `
            <option value='0'>Выберите тип рассылки</option>
            `;
    }

    // тип разметки для проекта
    function mailingTypeBtn() {
        let textPrListValue = textPrList.value;
        for (let i = 0; i < data.length; i++) {
            if (textPrListValue === data[i].name && mailingType.length <= data[i].type.length) {
                for (let j = 0; j < data[i].type.length; j++) {
                    let option2 = document.createElement('option');
                    option2.textContent = data[i].type[j][0];
                    mailingType.appendChild(option2);
                }
                numberOfSegments.value = 0;
                calendar.value = 0;
            }
        }
        textPrList.addEventListener("click", deleteOption);
    }
    mailingType.addEventListener("click", mailingTypeBtn);
    // создаём разметку для текста
    let segmentsArr = [];
    function updateMarkup() {
        while (containerForText.firstChild) {
            containerForText.removeChild(containerForText.firstChild);
        };
        for (let i = 0; i < data.length; i++) {
            let mailingTypeText = mailingType.value;
            let textPrListValue = textPrList.value;
            if (textPrListValue === data[i].name) {
                for (let t = 0; t < data[i].segments.length; t++) {
                    if (mailingTypeText === data[i].segments[t][0]) {
                        numberOfSegments.value = data[i].segments[t][1].length;
                        for(let m = 0; m < data[i].segments[t][1].length; m++) {
                            segmentsArr.push(data[i].segments[t][1][m])
                        }
                    }
                }
            }
            
        }
    }
    calendar.addEventListener("click", updateMarkup);
    function createMailingText() {
        let textPrListValue = textPrList.value;
        let mailingTypeText = mailingType.value;
        let num = numberOfSegments.value;
        while (containerForText.firstChild) {
            containerForText.removeChild(containerForText.firstChild);
        };
        for (let a = 0; a < num; a++) {
            let div = document.createElement('div');
            div.className = `text${a}`;
            div.innerHTML = `
                <div class="text-container">
                    <div  class="text-container2">
                        <div class="text-header-block"></div>
                        <div class="textBox"></div>
                    </div>
                </div>
            `;
            containerForText.appendChild(div);
            let textHeaderBlock = document.querySelectorAll(".text-header-block");
            let textContainer = document.querySelectorAll(".textBox");
            for (let i = 0; i < data.length; i++) {
                if (textPrListValue === data[i].name) {
                    for (let t = 0; t < data[i].type.length; t++) {
                        if (mailingTypeText.toString() === data[i].type[t][0]) {                                
                                textHeaderBlock[a].innerHTML = `
                                    <div>
                                        <p>Language: <span class="Language">${data[i].language[0]}</span></p>
                                    <div class="text-box">
                                        <span>Segment:</span>
                                        <div class="textarea">
                                            <input class="segment" type="text" style="width:100%;"/>
                                        </div>
                                    </div>
                                    <div class="text-box">
                                        <span>Subject:</span>
                                        <div class="textarea">
                                            <textarea name="subject" class="subject"
                                                placeholder="Введите тему письма" class="form-control"
                                                rows="1"></textarea>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            for (let s = 0; s < data[i].type[t][1].length; s++) {
                                if (data[i].type[t][1][s] === "text" || data[i].type[t][1][s] === "text1" || data[i].type[t][1][s] === "text2" || data[i].type[t][1][s] === "text3") {
                                    let div = document.createElement('div');
                                    div.className = 'text-box';
                                    div.innerHTML = `
                                    <span class="text-title">${data[i].type[t][1][s]}</span>
                                        <div class="textarea">
                                            <textarea name="subject" wrap="hard" class="projectText"  id="${data[i].type[t][1][s]}"
                                                placeholder="Введите текст письма" class="form-control"
                                                rows="4"></textarea>
                                        </div>
                                    `;
                                    textContainer[a].appendChild(div);
                                } else if (data[i].type[t][1][s] === "button_name" || data[i].type[t][1][s] === "button_name1" || data[i].type[t][1][s] === "button_name2" || data[i].type[t][1][s] === "button_name3") {
                                    let div = document.createElement('div');
                                    div.className = 'text-box';
                                    div.innerHTML = `
                                        <span>${data[i].type[t][1][s]}</span>
                                        <div class="textarea">
                                            <input class="projectText" id="${data[i].type[t][1][s]}" type="text" style="width:50%;"/>
                                        </div>
                                    `;
                                    textContainer[a].appendChild(div);
                                } else if (data[i].type[t][1][s] === "header1" || data[i].type[t][1][s] === "header2" || data[i].type[t][1][s] === "header3") {
                                    let div = document.createElement('div');
                                    div.className = 'text-box';
                                    div.innerHTML = `
                                    <span class="text-title">${data[i].type[t][1][s]}</span>
                                        <div class="textarea">
                                            <textarea name="subject" wrap="hard" class="projectText" id="${data[i].type[t][1][s]}"
                                                placeholder="Введите текст" class="form-control"
                                                rows="2"></textarea>
                                        </div>
                                    `;
                                    textContainer[a].appendChild(div);
                                }
                            }
                            let segment = document.querySelectorAll(".segment");
                            for (let b = 0; b < segment.length; b++) {
                                segment[b].value = segmentsArr[b];
                            }
                        }
                    }
                    
                }
            }
        }
    }
    createMailingTextBtn.addEventListener("click", createMailingText);
    // сохраняем текст

    function saveText() {
        let num = numberOfSegments.value;
        for (let a = 0; a < num; a++) {
            let sendTextArr = [];
            let textPrListValue = textPrList.value;
            let mailingTypeText = mailingType.value;
            let segment = document.querySelectorAll(".segment");
            let subject = document.querySelectorAll(".subject");
            let text = document.querySelectorAll(`.container-for-text .text${a} .projectText`);
            let date;
            let projectType = "projectType=" + mailingType.value;
            if (calendar.value) {
                date = "date=" + calendar.value;
            } else {
                let date2 = new Date();
                let year = date2.getFullYear();
                let month = date2.getMonth();
                let day = date2.getDate();
                if (month < 10) {
                    month = "0" + month;
                };
                if (day < 10) {
                    day = "0" + day;
                };
                date = "date=" + year + "-" + day + "-" + month;
            }
            let projectSegment = "projectSegment=" + segment[a].value.replace(/\s+/g, '+');
            let projectSubject = "subject_ru=" + subject[a].value.replace(/\s+/g, '+');
            projectName = "projectName=" + textPrList.value;
            projectName = projectName.replace(/\s+/g, '');
            sendTextArr.push(projectName);
            sendTextArr.push(projectType);
            sendTextArr.push(date);
            sendTextArr.push(projectSubject);
            let sendText = "";
            for (let i = 0; i < data.length; i++) {
                if (textPrListValue === data[i].name) {
                    sendText = "projectId=" + data[i].id;
                    let textLanguage = "textLanguage=" + data[i].language[0];
                    for(let l = 0; l < data[i].language.length; l++) {
                        if(data[i].language[l] !== "ru") {
                            projectSubject = "subject_" + data[i].language[l] + "=";
                            sendTextArr.push(projectSubject);
                        }
                    }
                    sendTextArr.push(textLanguage);
                    sendTextArr.push(projectSegment);
                    for (let t = 0; t < data[i].type.length; t++) {
                        if (mailingTypeText.toString() === data[i].type[t][0]) {
                            for (let s = 0; s < data[i].type[t][1].length; s++) {
                                for (let q = 0; q < text.length; q++) {
                                    if (data[i].type[t][1][s] === text[q].id) {
                                        let p = document.createElement('p');
                                        p.innerHTML = text[q].value;
                                        let text2 = p.innerHTML.replace("\n", "<br/>");
                                        let projectText = "" + data[i].type[t][1][s] + "_ru" + "=" + text2.replace(/\s+/g, '+');
                                        sendTextArr.push(projectText);
                                        for(let l = 0; l < data[i].language.length; l++) {
                                            if(data[i].language[l] !== "ru") {
                                                let projectText = "" + data[i].type[t][1][s] + "_" + data[i].language[l] + "=";
                                                sendTextArr.push(projectText);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // объект для отправки
            for (let t = 0; t < sendTextArr.length; t++) {
                sendText += "&" + sendTextArr[t];
            }
            var request = new XMLHttpRequest();

            function reqReadyStateChange() {
                if (request.readyState == 4 && request.status == 200)
                    document.getElementById("output").innerHTML = request.responseText;
            }
            // строка с параметрами для отправки
            request.open("POST", "save_text.php?" + sendText);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            request.onreadystatechange = reqReadyStateChange;
            request.send(sendText);
            console.log(sendText);
        }
    };
    document.querySelector("#saveTextBtn").addEventListener("click", saveText);
});