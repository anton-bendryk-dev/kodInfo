let calendar = document.querySelector(".calendar");
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
        let languageArr = [];
        let num = 0;
        // ================================================
        // достаём названия проектов
        // ================================================
        for (let i = 0; i < dataText.length; i++) {
            calendar = document.querySelector(".calendar");
            if (mailingType.value.replace(/\s+/g, "") !== "Regular") {
                calendar.value = dataText[i].date;
            }
            if (dataText[i].projectName === textPrList.value.replace(/\s+/g, "") && dataText[i].projectType === mailingType.value.replace(/\s+/g, "") && dataText[i].date === calendar.value) {
                idArr.push(dataText[i].id);
                let idProject = dataText[i].id;
                let div = document.createElement("div");
                let slider = document.createElement("div");
                slider.className = "one-time";
                slider.id = `${dataText[i].textLanguage}`;
                containerForText.appendChild(div);
                containerForText.appendChild(slider);
                for (let j = 0; j < dataText[i].textLanguage.length; j++) {
                    for (const prop of Object.keys(dataText[i])) {
                        if (dataText[i].textLanguage[j] === prop) {
                            let div2 = document.createElement("div");
                            div2.innerHTML = `<div class="text-container">
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
                                                                    <div class="textarea subject${num}">
                                                                        <textarea name="subject" class="subject" id="subject${dataText[i].textLanguage[j]}"
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
                            if (dataText[i].textLanguage[j] === "ru") {
                                div.appendChild(div2);
                            } else {
                                slider.appendChild(div2);
                                languageArr.push(dataText[i].textLanguage[j]);
                            };
                            let segment = document.querySelector(`.text-container${idProject + dataText[i].textLanguage[j]} .segment`);
                            let subject = document.querySelector(`.text-container${idProject + dataText[i].textLanguage[j]} .subject${num} #subject${dataText[i].textLanguage[j]}`);
                            segment.value = dataText[i].projectSegment;
                            subject.value = dataText[i][`${prop}`][0][1];
                            let textBox2 = document.querySelector(`.text-container${idProject + dataText[i].textLanguage[j]} .text-box2`);
                            for (let k = 1; k < dataText[i][`${prop}`].length; k++) {
                                if (dataText[i][`${prop}`][k][0] === "text" || dataText[i][`${prop}`][k][0] === "text1" || dataText[i][`${prop}`][k][0] === "text2" || dataText[i][`${prop}`][k][0] === "text3") {
                                    let div = document.createElement("div");
                                    div.className = "text-box";
                                    let textClass = dataText[i][`${prop}`][k][0];
                                    let text = dataText[i][`${prop}`][k][1].replace("<br/> ", "\n\n");
                                    div.innerHTML = `<span class="text-title">${textClass}</span>
                                                            <div class="textarea  textarea${num}">
                                                                <textarea name="text" class="projectText"
                                                                    placeholder="Введите текст письма" id=${textClass} rows="4">${text}</textarea>
                                                            </div>`;
                                    textBox2.appendChild(div);
                                } else if (dataText[i][`${prop}`][k][0] === "button_name" || dataText[i][`${prop}`][k][0] === "button_name1" || dataText[i][`${prop}`][k][0] === "button_name2" || dataText[i][`${prop}`][k][0] === "button_name3") {
                                    let div = document.createElement("div");
                                    div.className = "text-box";
                                    let textClass = dataText[i][`${prop}`][k][0];
                                    div.innerHTML = `<span class="text-title">${textClass}</span>
                                                                    <div class="textarea input${num} textarea${num}">
                                                                        <input class="projectText" id="${textClass}" type="text" style="width:50%;"/>
                                                                    </div>
                                                                    `;
                                    textBox2.appendChild(div);
                                    let input = document.querySelector(`.input${num} #${textClass}`);

                                    input.value = dataText[i][`${prop}`][k][1];
                                } else if (dataText[i][`${prop}`][k][0] === "header0" || dataText[i][`${prop}`][k][0] === "header1" || dataText[i][`${prop}`][k][0] === "header2" || dataText[i][`${prop}`][k][0] === "header3") {
                                    let div = document.createElement("div");
                                    div.className = "text-box";
                                    let textClass = dataText[i][`${prop}`][k][0];
                                    let text = dataText[i][`${prop}`][k][1].replace("<br/>", "\n\n");

                                    div.innerHTML = `<span class="text-title">${textClass}</span>
                                                                    <div class="textarea  textarea${num}">
                                                                        <textarea name="text" wrap="hard" class="projectText" id="${textClass}" placeholder="Введите текст" class="form-control"
                                                                            rows="2">${text}</textarea>
                                                                    </div>`;
                                    textBox2.appendChild(div);
                                }
                            }
                        }
                    }
                    num++
                }
            }
        }
        sliderFunction();
        let lannguageBtns = document.querySelectorAll(".slick-dots button");
        for (let c = 0; c < languageArr.length; c++) {
            lannguageBtns[c].innerHTML = languageArr[c];
        }

        // ================================================
        // сохраняем текст
        // ================================================
        num = 0;
        let sendTextData = [];

        function CreateText() {
            for (let a = 0; a < idArr.length; a++) {
                for (let i = 0; i < dataText.length; i++) {
                    if (dataText[i].id === idArr[a]) {
                        let textTitle = document.querySelectorAll(`.text-container${idArr[a]}ru .text-title`);
                        let textTitleArr = [];
                        for (let r = 0; r < textTitle.length; r++) {
                            textTitleArr.push(textTitle[r].textContent)
                        }
                        
                        let date = "date=" + calendar.value;
                        let projectType = "projectType=" + mailingType.value;
                        let projectName = "projectName=" + textPrList.value.replace(/\s+/g, "");
                        let segment = document.querySelector(`.text-container${idArr[a]}ru .segment`);
                        let projectSegment = "projectSegment=" + segment.value.replace(/\s+/g, "+");
                        let sendTextArr = [];
                        sendTextArr.push(projectName);
                        sendTextArr.push(projectType);
                        sendTextArr.push(date);
                        sendTextArr.push(projectSegment);
                        for (let j = 0; j < dataText[i].textLanguage.length; j++) {
                            let subject1 = document.querySelector(`.text-container${idArr[a] + dataText[i].textLanguage[j]} .subject${num} #subject${dataText[i].textLanguage[j]}`);
                            let projectSubject = "subject_" + dataText[i].textLanguage[j] + "=" + subject1.value.replace(/\s+/g, "+");
                            sendTextArr.push(projectSubject);
                            let text = [];
                            for (let m = 0; m < textTitleArr.length; m++) {
                                text.push(document.querySelector(`.text-container${idArr[a] + dataText[i].textLanguage[j]} .textarea${num} #${textTitleArr[m]}`))
                            };
                            
                            num++;
                            for (let t = 0; t < text.length; t++) {
                                let p = document.createElement("p");
                                p.innerHTML = text[t].value;
                                let text2 = p.innerHTML.replace("\n", "<br/>");
                                let projectText = `${text[t].id}` + "_" + dataText[i].textLanguage[j] + "=" + text2.replace(/\s+/g, "+");
                                sendTextArr.push(projectText);
                            };
                            
                        }

                        // объект для отправки
                        let id = "id=" + idArr[a];
                        let sendText = id;
                        for (let t = 0; t < sendTextIdtArr.length; t++) {
                            sendText += "&"+ sendTextIdtArr[t];
                        }
                        for (let t = 0; t < sendTextArr.length; t++) {
                            sendText += "&" + sendTextArr[t];
                        }
                        
                        sendTextData.push(sendText)
                    }
                }
            }
            
            num = 0;
        }

        function sendTextFunction() {
            CreateText();
            document.querySelector(".save-text-container").style.display = 'flex';
            for (let text of sendTextData) {
                function sendText() {
                    let request = new XMLHttpRequest();
                    function reqReadyStateChange() {
                        if (request.readyState === XMLHttpRequest.DONE && request.status === 200) {
                            console.log(request.responseText);
                        } else {
                            console.log(request.status);
                        };
                    }
                    // строка с параметрами для отправки
                    request.open("POST", "../save_text.php?", false);
                    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    request.setRequestHeader('Content-Length', `${text.length}`);
                    request.send(text);
                    request.onreadystatechange = reqReadyStateChange;
                    console.log(text);
                }
                sendText();
            }
            
            window.setTimeout(function () {
                localStorage.clear();
                location.reload();
            }, 1500);
        }
        document.querySelector("#saveTextBtn").addEventListener("click", sendTextFunction);
    });
}
createMailingTextBtn.addEventListener("click", getText);