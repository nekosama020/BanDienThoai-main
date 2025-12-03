<!-- includes/chatbox.php -->
<style>
    /* --- CSS CŨ (Giữ nguyên) --- */
    #chat-circle { position: fixed; bottom: 20px; left: 20px; z-index: 9999; width: 60px; height: 60px; border-radius: 50%; background: #0d6efd; color: white; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.3); cursor: pointer; font-size: 30px; display: flex; align-items: center; justify-content: center; transition: transform 0.2s; }
    #chat-circle:hover { transform: scale(1.1); }
    #chat-box { position: fixed; bottom: 90px; left: 20px; z-index: 9999; width: 350px; height: 500px; background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.2); display: none; flex-direction: column; overflow: hidden; font-family: Arial, sans-serif; border: 1px solid #ddd; }
    .c-head { background: #0d6efd; color: white; padding: 15px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
    .c-body { flex: 1; padding: 15px; overflow-y: auto; background: #f8f9fa; display: flex; flex-direction: column; gap: 10px; }
    .c-foot { padding: 10px; display: flex; border-top: 1px solid #eee; background: white; align-items: center; gap: 5px; } /* Thêm gap */
    .c-foot input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 20px; outline: none; font-size: 14px; }
    .msg { padding: 10px 14px; border-radius: 15px; max-width: 80%; word-wrap: break-word; font-size: 14px; line-height: 1.5; }
    .bot { background: #e9ecef; color: #333; align-self: flex-start; border-bottom-left-radius: 2px; }
    .user { background: #0d6efd; color: white; align-self: flex-end; border-bottom-right-radius: 2px; }
    .typing { font-style: italic; color: #888; font-size: 12px; margin-left: 10px; }

    /* --- CSS ẢNH --- */
    #preview-area { display: none; padding: 10px; background: #eee; border-top: 1px solid #ddd; position: relative; }
    .img-thumb { max-width: 80px; max-height: 80px; border-radius: 8px; border: 1px solid #999; }
    .btn-close-img { position: absolute; top: 5px; left: 85px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px; font-weight: bold; }

    /* --- CSS CHO MICRO (MỚI) --- */
    #btn-mic {
        background: none; border: 1px solid #ddd; border-radius: 50%;
        width: 40px; height: 40px; cursor: pointer; color: #555; font-size: 18px;
        display: flex; align-items: center; justify-content: center;
        transition: 0.3s;
    }
    #btn-mic:hover { background: #f0f0f0; }
    
    /* Hiệu ứng khi đang nghe */
    #btn-mic.listening {
        background: #dc3545; color: white; border-color: #dc3545;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
</style>

<button id="chat-circle" onclick="toggleChat()">💬</button>

<div id="chat-box">
    <div class="c-head">
        <span>Trợ lý AI (Ảnh & Giọng nói)</span>
        <span onclick="toggleChat()" style="cursor:pointer; font-size: 18px;">✖</span>
    </div>
    
    <div class="c-body" id="c-body">
        <div class="msg bot">Chào bạn! Bạn có thể nói chuyện, gửi ảnh hoặc chat với mình nhé! 🎤📷</div>
    </div>
    
    <!-- VÙNG XEM TRƯỚC ẢNH -->
    <div id="preview-area">
        <img id="img-preview" class="img-thumb" src="">
        <button class="btn-close-img" onclick="removeImage()">X</button>
    </div>

    <div class="c-foot">
        <!-- NÚT CAMERA -->
        <label for="img-input" style="cursor: pointer; font-size: 22px; color: #555;" title="Gửi ảnh">📷</label>
        <input type="file" id="img-input" accept="image/*" style="display: none;" onchange="handleImage()">

        <!-- Ô NHẬP LIỆU -->
        <input type="text" id="c-inp" placeholder="Nhập hoặc bấm Mic để nói..." onkeypress="if(event.key==='Enter') sendChat()">
        
        <!-- NÚT MICRO (MỚI) -->
        <button id="btn-mic" onclick="startVoice()" title="Bấm để nói tiếng Việt">🎤</button>

        <!-- NÚT GỬI -->
        <button onclick="sendChat()" style="background:none; border:none; color:#0d6efd; font-weight:bold; cursor:pointer; font-size: 20px;">➤</button>
    </div>
</div>

<script>
    let base64Image = ""; 
    // Biến nhận diện giọng nói
    let recognition;

    // --- CẤU HÌNH NHẬN DIỆN GIỌNG NÓI ---
    if ('webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.continuous = false; // Nói xong 1 câu tự ngắt
        recognition.lang = 'vi-VN'; // Bắt buộc Tiếng Việt
        recognition.interimResults = false;

        recognition.onstart = function() {
            document.getElementById('btn-mic').classList.add('listening');
            document.getElementById('c-inp').placeholder = "Đang nghe bạn nói...";
        };

        recognition.onend = function() {
            document.getElementById('btn-mic').classList.remove('listening');
            document.getElementById('c-inp').placeholder = "Nhập tin nhắn...";
        };

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            document.getElementById('c-inp').value = transcript;
            // Tự động gửi sau 1s để người dùng kịp nhìn thấy chữ
            setTimeout(sendChat, 500);
        };
        
        recognition.onerror = function(event) {
            console.error("Lỗi giọng nói:", event.error);
            document.getElementById('btn-mic').classList.remove('listening');
            alert("Không nghe rõ hoặc chưa cấp quyền Micro.");
        };
    } else {
        document.getElementById('btn-mic').style.display = 'none'; // Ẩn nút nếu trình duyệt không hỗ trợ
    }

    function startVoice() {
        if (recognition) {
            recognition.start();
        } else {
            alert("Trình duyệt của bạn không hỗ trợ giọng nói.");
        }
    }

    function toggleChat() {
        const box = document.getElementById('chat-box');
        box.style.display = (box.style.display === 'flex') ? 'none' : 'flex';
    }

    // --- XỬ LÝ ẢNH & GỬI TIN (GIỮ NGUYÊN NHƯ CŨ) ---
    function handleImage() {
        const file = document.getElementById('img-input').files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                base64Image = e.target.result.split(',')[1]; 
                document.getElementById('img-preview').src = e.target.result;
                document.getElementById('preview-area').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    function removeImage() {
        base64Image = "";
        document.getElementById('img-input').value = "";
        document.getElementById('preview-area').style.display = 'none';
    }

    async function sendChat() {
        const inp = document.getElementById('c-inp');
        const txt = inp.value.trim();
        
        if (!txt && !base64Image) return;

        let userHtml = txt;
        if (base64Image) {
            userHtml += `<br><img src="data:image/jpeg;base64,${base64Image}" style="max-width:150px; border-radius:10px; margin-top:5px;">`;
        }
        addMsgHTML(userHtml, 'user');

        inp.value = '';
        let imgToSend = base64Image; 
        removeImage(); 

        const botMsgId = addMsgHTML('Đang suy nghĩ...', 'bot');

        try {
            let basePath = window.location.pathname.includes('/admin/') || window.location.pathname.includes('/pages/') ? '../' : '';
            
            const res = await fetch(basePath + 'chat_server.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    message: txt,
                    image: imgToSend
                })
            });
            
            const data = await res.json();
            
            const botBubble = document.getElementById(botMsgId);
            if (botBubble) {
                botBubble.innerHTML = data.reply.replace(/\n/g, "<br>");
            }
            
        } catch (e) {
            const botBubble = document.getElementById(botMsgId);
            if (botBubble) botBubble.innerText = "Lỗi kết nối: " + e.message;
        }
    }

    function addMsgHTML(html, type) {
        const div = document.createElement('div');
        const uniqueId = 'msg-' + Date.now() + Math.random();
        div.id = uniqueId;
        div.className = 'msg ' + type;
        div.innerHTML = html;
        
        const body = document.getElementById('c-body');
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
        return uniqueId;
    }
</script>