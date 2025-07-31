<!-- ...existing code... -->
<div class="max-w-4xl mx-auto mt-8 p-6 bg-white dark:bg-[#161615] rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4 text-[#F53003]">Money Transfer API Endpointleri</h2>
    <ul class="space-y-4 text-sm leading-normal">
        <li>
            <strong>Kimlik Doğrulama:</strong>
            <ul class="ml-4">
                <li>POST <code>http://10.190.1.115:8081/api/register</code>: Yeni bir kullanıcı kaydeder.</li>
                <li>POST <code>http://10.190.1.115:8081/api/login</code>: Bir kullanıcıyı oturum açar.</li>
                <li>POST <code>http://10.190.1.115:8081/api/logout</code>: Bir kullanıcının oturumunu kapatır. (<span class="text-[#706f6c]">auth:sanctum gerektirir</span>)</li>
            </ul>
        </li>
        <li>
            <strong>Ödeme Terimleri (Payment Terms):</strong>
            <ul class="ml-4">
                <li>GET <code>http://10.190.1.115:8081/api/payment-terms</code>: Ödeme terimlerinin listesini alır.</li>
                <li>POST <code>http://10.190.1.115:8081/api/payment-terms</code>: Yeni bir ödeme terimi oluşturur.</li>
                <li>GET <code>http://10.190.1.115:8081/api/payment-terms/{payment_term}</code>: Belirli bir ödeme terimini alır.</li>
                <li>PUT/PATCH <code>http://10.190.1.115:8081/api/payment-terms/{payment_term}</code>: Belirli bir ödeme terimini günceller.</li>
                <li>DELETE <code>http://10.190.1.115:8081/api/payment-terms/{payment_term}</code>: Belirli bir ödeme terimini siler.</li>
            </ul>
        </li>
        <li>
            <strong>İşlem Endpointleri (Transactions):</strong>
            <ul class="ml-4">
                <li>GET <code>http://10.190.1.115:8081/api/transactions</code>: İşlemlerin bir listesini alır.</li>
                <li>POST <code>http://10.190.1.115:8081/api/transactions</code>: Yeni bir işlem oluşturur.</li>
                <li>GET <code>http://10.190.1.115:8081/api/transactions/{transaction}</code>: Belirli bir işlemi alır.</li>
                <li>DELETE <code>http://10.190.1.115:8081/api/transactions/{transaction}</code>: Belirli bir işlemi siler.</li>
            </ul>
        </li>
        <li>
            <strong>Kullanıcı Endpointleri (Users):</strong>
            <ul class="ml-4">
                <li>GET <code>http://10.190.1.115:8081/api/users</code>: Kullanıcıların bir listesini alır.</li>
                <li>GET <code>http://10.190.1.115:8081/api/users/{user}</code>: Belirli bir kullanıcıyı alır.</li>
                <li>PUT/PATCH <code>http://10.190.1.115:8081/api/users/{user}</code>: Belirli bir kullanıcıyı günceller.</li>
                <li>DELETE <code>http://10.190.1.115:8081/api/users/{user}</code>: Belirli bir kullanıcıyı siler.</li>
            </ul>
        </li>
    </ul>
</div>
<!-- ...existing code... -->