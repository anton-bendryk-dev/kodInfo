let calendar = document.querySelector(".calendar");
let containerForText = document.querySelector(".container-for-text");
let createMailingTextBtn = document.querySelector("#createMailingTextBtn");

function getText() {
    // ================================================
    // формируем путь к JSON
    // ================================================
    let way = "./texts/" + textPrList.value.replace(/\s+/g, "") + "/" + mailingType.value.replace(/\s+/g, "") + "/textsData.json";

    function readTextFile(file, callback) {
        var rawFile = new XMLHttpRequest();
        rawFile.overrideMimeType(`${way}`);
        rawFile.open("GET", file, true);
        rawFile.onreadystatechange = function () {
            if (rawFile.readyState === 4 && rawFile.status == "200") {
                callback(rawFile.responseText);
            }
        };
        rawFile.setRequestHeader("Cache-Control", "no-cache");
        rawFile.send(null);
    }
    readTextFile(`${way}`, function (text) {
        var dataText = JSON.parse(text);

        let idArr = [];
        // ================================================
        // достаём названия проектов
        // ================================================
        for (let i = 0; i < dataText.length; i++) {
            calendar = document.querySelector(".calendar");
            if (dataText[i].projectName === textPrList.value.replace(/\s+/g, "") && dataText[i].projectType === mailingType.value.replace(/\s+/g, "") && dataText[i].date === calendar.value) {
                idArr.push(dataText[i].id);
                for (let j = 0; j < dataText[i].textLanguage.length; j++) {
                    for (const prop of Object.keys(dataText[i])) {
                        if (prop === dataText[i].textLanguage[j]) {
                            let div = document.createElement("div");
                            let idProject = dataText[i].id;
                            div.innerHTML = `<div class="text-container">
                                                    <div class="text-container${idProject + dataText[i].textLanguage[j]}">
                                                        <div class="text-header-block"><div>
                                                        <p>Language: <span class="Language">${dataText[i].textLanguage[j]}</span></p>
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
                                                                            placeholder="Введите тему письма"
                                                                            rows="1"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-box2"></div>
                                                    </div>
                                                </div>
                                            `;
                            containerForText.appendChild(div);
                            let segment = document.querySelector(`.text-container${idProject + dataText[i].textLanguage[j]} .segment`);
                            let subject = document.querySelector(`.text-container${idProject + dataText[i].textLanguage[j]} .subject`);
                            segment.value = dataText[i].projectSegment;
                            subject.value = dataText[i][`${prop}`][0][1];

                            let textBox2 = document.querySelector(`.text-container${idProject + dataText[i].textLanguage[j]} .text-box2`);
                            for (let k = 1; k < dataText[i][`${prop}`].length; k++) {
                                if (dataText[i][`${prop}`][k][0] === "text" || dataText[i][`${prop}`][k][0] === "text1" || dataText[i][`${prop}`][k][0] === "text2" || dataText[i][`${prop}`][k][0] === "text3") {
                                    let div = document.createElement("div");
                                    div.className = "text-box";
                                    let textClass = dataText[i][`${prop}`][k][0];
                                    let text = dataText[i][`${prop}`][k][k];
                                    text.replace("<br/>", "\n\n");
                                    div.innerHTML = `<span class="text-title">${textClass}</span>
                                                        <div class="textarea">
                                                            <textarea name="subject" class="projectText"
                                                                placeholder="Введите текст письма" id=${textClass} rows="4">${text}</textarea>
                                                        </div>`;
                                    textBox2.appendChild(div);
                                } else if (dataText[i][`${prop}`][k][0] === "button_name" || dataText[i][`${prop}`][k][0] === "button_name1" || dataText[i][`${prop}`][k][0] === "button_name2" || dataText[i][`${prop}`][k][0] === "button_name3") {
                                    let div = document.createElement("div");
                                    div.className = "text-box";
                                    let textClass = dataText[i][`${prop}`][k][0];
                                    div.innerHTML = `<span class="text-title">${textClass}</span>
                                                                <div class="textarea">
                                                                    <input class="projectText" id="${textClass}" type="text" style="width:50%;"/>
                                                                </div>
                                                                `;
                                    textBox2.appendChild(div);
                                    let input = document.querySelector(`#${textClass}`);
                                    input.value = dataText[i][`${prop}`][k][1];
                                } else if (dataText[i][`${prop}`][k][0] === "header1" || dataText[i][`${prop}`][k][0] === "header2" || dataText[i][`${prop}`][k][0] === "header3") {
                                    let div = document.createElement("div");
                                    div.className = "text-box";
                                    let textClass = dataText[i][`${prop}`][k][0];
                                    let text = dataText[i][`${prop}`][k][1];
                                    text.replace("<br/>", "\n\n");
                                    div.innerHTML = `<span class="text-title">${textClass}</span>
                                                                <div class="textarea">
                                                                    <textarea name="subject" wrap="hard" class="projectText" id="${textClass}" placeholder="Введите текст" class="form-control"
                                                                        rows="2">${text}</textarea>
                                                                </div>`;
                                    textBox2.appendChild(div);
                                }
                            }
                        }
                    }
                }
            }
        }
        // ================================================
        // очистить разметку
        // ================================================
        function updateMarkup() {
            while (containerForText.firstChild) {
                containerForText.removeChild(containerForText.firstChild);
            }
        }
        calendar.addEventListener("click", updateMarkup);
        // ================================================
        // сохраняем текст
        // ================================================

        function saveText() {
            for (let a = 0; a < idArr.length; a++) {
                for (let i = 0; i < dataText.length; i++) {
                    if (dataText[i].id === idArr[a]) {
                        let date = "date=" + calendar.value;
                        let projectType = "projectType=" + mailingType.value;
                        let projectName = "projectName=" + textPrList.value.replace(/\s+/g, "");
                        let segment = document.querySelector(`.text-container${idArr[a]}ru .segment`);
                        let projectSegment = "projectSegment=" + segment.value.replace(/\s+/g, "+");
                        let sendTextArr2 = [];
                        sendTextArr2.push(projectName);
                        sendTextArr2.push(projectType);
                        sendTextArr2.push(date);
                        sendTextArr2.push(projectSegment);
                        for (let j = 0; j < dataText[i].textLanguage.length; j++) {
                            let subject = document.querySelector(`.text-container${idArr[a] + dataText[i].textLanguage[j]} .subject`);
                            let projectSubject = "subject_" + dataText[i].textLanguage[j] + "=" + subject.value.replace(/\s+/g, "+");
                            let text = document.querySelectorAll(`.text-container${idArr[a] + dataText[i].textLanguage[j]} .projectText`);
                            sendTextArr2.push(projectSubject);
                            for (let t = 0; t < text.length; t++) {
                                let p = document.createElement("p");
                                p.innerHTML = text[t].value;
                                let text2 = p.innerHTML.replace("\n", "<br/>");
                                let projectText = `${text[t].id}` + "_" + dataText[i].textLanguage[j] + "=" + text2.replace(/\s+/g, "+");
                                sendTextArr2.push(projectText);
                            }
                        }

                        // объект для отправки
                        let id = "id=" + idArr[a];
                        let sendText = id;
                        for (let t = 0; t < sendTextArr.length; t++) {
                            sendText += "&" + sendTextArr[t];
                        }
                        for (let a = 0; a < sendTextArr2.length; a++) {
                            sendText += "&" + sendTextArr2[a];
                        }
                        var request = new XMLHttpRequest();

                        function reqReadyStateChange() {
                            if (request.readyState === XMLHttpRequest.DONE && request.status === 200) {
                                console.log(request.responseText);
                            };
                        }
                        // строка с параметрами для отправки
                        request.open("POST", "../save_text.php?" + sendText);
                        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        request.onreadystatechange = reqReadyStateChange;
                        request.send(sendText);
                        localStorage.clear();
                        alert("Текст сохранился");
                        console.log(sendText);
                    }
                }
            }
            window.setTimeout(function () {
                location.reload();
            }, 1000);
        }
        document.querySelector("#saveTextBtn").addEventListener("click", saveText);
    });
}
createMailingTextBtn.addEventListener("click", getText);
