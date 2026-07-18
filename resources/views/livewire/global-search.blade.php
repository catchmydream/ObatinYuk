<div class="global-search-wrapper" style="position: relative; width: 100%; max-width: 400px; margin: 0 1rem;">
    <form action="/search" method="GET" style="position: relative; margin: 0;">
        <input 
            type="text" 
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari obat atau gejala..." 
            style="width: 100%; padding: 10px 16px 10px 40px; border-radius: 20px; border: 1.5px solid #e2e8f0; font-size: 0.9rem; outline: none; transition: all 0.2s; background: #f8fafc; font-family: 'Inter', sans-serif;"
            onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16,185,129,0.1)';"
            onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
        >
        <button type="submit" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 0; cursor: pointer; color: #94a3b8; display: flex; align-items: center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </button>
    </form>
</div>
