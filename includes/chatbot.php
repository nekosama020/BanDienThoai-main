<style>
    /* Nút chat tròn */
    #chat-circle {
        position: fixed; bottom: 20px; left: 20px; z-index: 9999;
        width: 60px; height: 60px; border-radius: 50%;
        background: #0d6efd; color: white; border: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3); cursor: pointer;
        font-size: 30px; display: flex; align-items: center; justify-content: center;
        transition: transform 0.2s;
    }
    #chat-circle:hover { transform: scale(1.1); }

    /* Khung chat */
    #chat-box {
        position: fixed; bottom: 90px; left: 20px; z-index: 9999;
        width: 320px; height: 450px; background: white;
        border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        display: none; flex-direction: column; overflow: hidden;
        font-family: Arial, sans-serif;
        border: 1px solid #ddd;
    }

    /* Header */
    .c-head { 
        background: #0d6efd; color: white; padding: 15px; 
        font-weight: bold; display: flex; justify-content: space-between; align-items: center;
    }
    
    /* Body (Chứa tin nhắn) */
    .c-body { 
        flex: 1; padding: 15px; overflow-y: auto; 
        background: #f8f9fa; display: flex; flex-direction: column; gap: 10px;
    }

    /* Footer (Ô nhập) */
    .c-foot { 
        padding: 10px; display: flex; border-top: 1px solid #eee; background: white; 
    }
    .c-foot input { 
        flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 20px; outline: none; font-size: 14px;
    }

    /* Style tin nhắn */
    .msg { 
        padding: 10px 14px; border-radius: 15px; 
        max-width: 80%; word-wrap: break-word; font-size: 14px; line-height: 1.5;
    }
    
    /* Tin nhắn BOT (Màu xám, bên trái) */
    .bot { 
        background: #e9ecef; color: #333; 
        align-self: flex-start; 
        border-bottom-left-radius: 2px;
    }
    
    /* Tin nhắn USER (Màu xanh, bên phải) */
    .user { 
        background: #0d6efd; color: white; 
        align-self: flex-end; 
        border-bottom-right-radius: 2px;
    }
    
    /* Hiệu ứng đang nhập */
    .typing { font-style: italic; color: #888; font-size: 12px; margin-left: 10px; }
</style>

<button id="chat-circle" onclick="toggleChat()">💬</button>

<div id="chat-box">
    <div class="c-head">
        <span>Trợ lý ảo AI</span>
        <span onclick="toggleChat()" style="cursor:pointer; font-size: 18px;">✖</span>
    </div>
    
    <div class="c-body" id="c-body">
        <div class="msg bot">Xin chào! Tôi có thể giúp gì cho bạn?</div>
    </div>
    
    <div class="c-foot">
        <input type="text" id="c-inp" placeholder="Hỏi về iPhone, Samsung..." onkeypress="if(event.key==='Enter') sendChat()">
        <button onclick="sendChat()" style="background:none; border:none; color:#0d6efd; font-weight:bold; cursor:pointer; margin-left:10px;">➤</button>
    </div>
</div>

<script>
    function toggleChat() {
        const box = document.getElementById('chat-box');
        box.style.display = (box.style.display === 'flex') ? 'none' : 'flex';
    }

    async function sendChat() {
        const inp = document.getElementById('c-inp');
        const txt = inp.value.trim();
        if (!txt) return;

        // 1. Hiện tin nhắn CỦA BẠN (User) trước
        addMsg(txt, 'user'); 
        inp.value = ''; // Xóa ô nhập liệu ngay lập tức

        // 2. Hiện tin nhắn "Đang nhập..." CỦA BOT và LẤY ID của nó
        const botMsgId = addMsg('Đang suy nghĩ...', 'bot');

        try {
            // Tự động tính đường dẫn
            let basePath = window.location.pathname.includes('/admin/') || window.location.pathname.includes('/pages/') ? '../' : '';
            
            const res = await fetch(basePath + 'chat_server.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({message: txt})
            });
            
            const data = await res.json();
            
            // 3. TÌM ĐÚNG cái bong bóng "Đang suy nghĩ..." (dựa vào ID) và thay thế nội dung
            const botBubble = document.getElementById(botMsgId);
            if (botBubble) {
                // Thay thế text, và render HTML (để hiển thị icon nếu có)
                botBubble.innerHTML = data.reply.replace(/\n/g, "<br>");
            }
            
        } catch (e) {
            const botBubble = document.getElementById(botMsgId);
            if (botBubble) botBubble.innerText = "Lỗi kết nối: " + e.message;
        }
    }

    // Hàm thêm tin nhắn vào khung chat
    function addMsg(txt, type) {
        const div = document.createElement('div');
        const uniqueId = 'msg-' + Date.now() + Math.random(); // Tạo ID ngẫu nhiên để không trùng
        div.id = uniqueId;
        div.className = 'msg ' + type;
        div.innerText = txt;
        
        const body = document.getElementById('c-body');
        body.appendChild(div);
        body.scrollTop = body.scrollHeight; // Tự cuộn xuống dưới cùng
        
        return uniqueId; // Trả về ID để lát nữa tìm mà sửa
    }
</script>