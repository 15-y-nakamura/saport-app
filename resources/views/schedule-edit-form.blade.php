<html>
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <form method="POST" action="{{ route('schedule-update') }}">
        @csrf
        <input type="hidden" name="id" value="{{ $event->id }}">
        <label>
            タイトル:
            <input type="text" name="event_name" value="{{ old('event_name', $event->event_name) }}" required>
        </label>
        <label>
            開始日時:
            <input type="datetime-local" name="start_date" value="{{ old('start_date', date('Y-m-d\TH:i', strtotime($event->start_date))) }}" required>
        </label>
        <label>
            終了日時:
            <input type="datetime-local" name="end_date" value="{{ old('end_date', date('Y-m-d\TH:i', strtotime($event->end_date))) }}" required>
        </label>
        <label>
            場所:
            <input type="text" name="location" value="{{ old('location', $event->location) }}">
        </label>
        <label>
            リンク:
            <input type="url" name="link" value="{{ old('link', $event->link) }}">
        </label>
        <label>
            メモ:
            <textarea name="memo">{{ old('memo', $event->memo) }}</textarea>
        </label>
        <button type="submit">保存</button>
        <button type="button" onclick="window.location.href='/calendar';">戻る</button> <!-- 戻るボタンの追加 -->
    </form>
</body>
</html>
