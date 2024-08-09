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

    // 日付をクリック、または範囲を選択したイベント
    selectable: true,
    select: function (info) {
        // 入力ダイアログ
        const eventName = prompt("イベントを入力してください");

        if (eventName) {
            // Laravelの登録処理の呼び出し
            axios
                .post("/schedule-add", {
                    start_date: info.start.valueOf(),
                    end_date: info.end.valueOf(),
                    event_name: eventName,
                })
                .then((response) => {
                    // イベントの追加
                    calendar.addEvent({
                        id: response.data.id, // イベントIDをセット
                        title: eventName,
                        start: info.start,
                        end: info.end,
                        allDay: true,
                    });
                })
                .catch(() => {
                    // バリデーションエラーなど
                    alert("登録に失敗しました");
                });
        }
    },

    // イベントの取得処理
    events: function (info, successCallback, failureCallback) {
        axios
            .post("/schedule-get", {
                start_date: info.start.valueOf(),
                end_date: info.end.valueOf(),
            })
            .then((response) => {
                // カレンダーに読み込み
                successCallback(response.data.map(event => ({
                    ...event,
                    id: event.id  // イベントIDをセット
                })));
            })
            .catch(() => {
                alert("イベントの取得に失敗しました");
            });
    },

    // イベントをクリックしたときの処理（編集および削除）
    eventClick: function(info) {
        // 編集か削除かを選択させる
        const action = prompt('イベントを編集する場合は名前を変更してください。\n削除する場合は「削除」と入力してください。', info.event.title);

        if (action === null) {
            // ユーザーがキャンセルした場合
            return;
        }

        if (action.toLowerCase() === '削除') {
            // 削除処理
            if (confirm("このイベントを削除しますか？")) {
                axios
                    .post("/schedule-delete", {
                        id: info.event.id,
                    })
                    .then(() => {
                        // カレンダーからイベントを削除
                        info.event.remove();
                    })
                    .catch(() => {
                        alert("削除に失敗しました");
                    });
            }
        } else {
            // 編集処理
            const eventName = action;
            if (eventName) {
                axios
                    .post("/schedule-update", {
                        id: info.event.id,
                        start_date: info.event.start.valueOf(),
                        end_date: info.event.end ? info.event.end.valueOf() : info.event.start.valueOf(), // endがない場合はstartと同じにする
                        event_name: eventName,
                    })
                    .then(() => {
                        // イベントの更新
                        info.event.setProp('title', eventName);
                    })
                    .catch(() => {
                        alert("更新に失敗しました");
                    });
            }
        }
    }
});

calendar.render();
