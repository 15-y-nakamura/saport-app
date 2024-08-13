import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import axios from 'axios';

var calendarEl = document.getElementById("calendar");

let calendar = new Calendar(calendarEl, {
    plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin],
    initialView: "dayGridMonth",
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,listWeek",
    },
    locale: "ja",

    selectable: true,
    editable: true,

    select: function (info) {
        const formData = new FormData();

        formData.append('start_date', info.start.valueOf());
        formData.append('end_date', info.end.valueOf());

        window.location.href = '/schedule-add-form?' + new URLSearchParams(formData).toString();
    },

    events: function (info, successCallback, failureCallback) {
        axios
            .post("/schedule-get", {
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then((response) => {
                successCallback(response.data.map(event => ({
                    ...event,
                    id: event.id
                })));
            })
            .catch(() => {
                alert("イベントの取得に失敗しました");
            });
    },

    eventClick: function(info) {
        const action = prompt('イベントを編集する場合は名前を変更してください。\n削除する場合は「削除」と入力してください。', info.event.title);

        if (action === null) {
            return;
        }

        if (action.toLowerCase() === '削除') {
            if (confirm("このイベントを削除しますか？")) {
                axios
                    .post("/schedule-delete", {
                        id: info.event.id,
                    })
                    .then(() => {
                        info.event.remove();
                    })
                    .catch(() => {
                        alert("削除に失敗しました");
                    });
            }
        } else {
            const form = promptForm(info.event);

            form.onsubmit = (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);

                axios
                    .post("/schedule-update", {
                        id: info.event.id,
                        start_date: info.event.start.valueOf(),
                        end_date: info.event.end ? info.event.end.valueOf() : info.event.start.valueOf(),
                        event_name: formData.get("event_name"),
                        location: formData.get("location"),
                        link: formData.get("link"),
                        memo: formData.get("memo"),
                    })
                    .then(() => {
                        info.event.setProp('title', formData.get("event_name"));
                        form.remove();
                    })
                    .catch(() => {
                        alert("更新に失敗しました");
                        form.remove();
                    });
            };
        }
    },

    eventDrop: function(info) {
        axios
            .post("/schedule-update", {
                id: info.event.id,
                start_date: info.event.start.valueOf(),
                end_date: info.event.end ? info.event.end.valueOf() : info.event.start.valueOf(),
                event_name: info.event.title,
            })
            .then(() => {
                alert("イベントが更新されました");
            })
            .catch(() => {
                alert("更新に失敗しました");
            });
    }
});

calendar.render();

// マウスホイールでの月移動を実装
calendarEl.addEventListener('wheel', function(event) {
    if (event.deltaY < 0) {
        calendar.prev(); // 前の月へ移動
    } else if (event.deltaY > 0) {
        calendar.next(); // 次の月へ移動
    }
    event.preventDefault(); // スクロールイベントを止める
});

// ドラッグ中に月を自動で移動する（ゆっくり移動）
let autoScrollInterval = null;
document.addEventListener('mousemove', function(event) {
    const buffer = 50; // 画面端から何ピクセルで移動をトリガーするか
    const x = event.clientX;
    const y = event.clientY;

    clearInterval(autoScrollInterval); // 前のスクロール処理をクリア

    autoScrollInterval = setInterval(function() {
        if (x < buffer) {
            calendar.prev(); // 画面左端に近づいたら前の月へ
        } else if (x > window.innerWidth - buffer) {
            calendar.next(); // 画面右端に近づいたら次の月へ
        } else if (y < buffer) {
            calendar.prev(); // 画面上端に近づいたら前の月へ
        } else if (y > window.innerHeight - buffer) {
            calendar.next(); // 画面下端に近づいたら次の月へ
        }
    }, 1000); // 1秒ごとに月移動をトリガー
});

document.getElementById('add-event-button').addEventListener('click', function() {
    window.location.href = '/schedule-add-form'; // 新しいイベント追加ページへの遷移
});
