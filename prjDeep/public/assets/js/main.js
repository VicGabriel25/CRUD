// CRUD Mundo - Sistema de Gerenciamento Geográfico
// Arquivo JavaScript principal

console.log('%c🌍 CRUD Mundo', 'color: #00ff88; font-size: 24px; font-weight: bold;');
console.log('%cSistema de Gerenciamento Geográfico', 'color: #b0b0c0; font-size: 14px;');

// Configurações
const CONFIG = {
    // Construir a URL da API dinamicamente a partir da URL atual para funcionar
    // quando o projeto está em um subdiretório (ex.: /prjDeep/public)
    API_BASE_URL: (function() {
        const origin = window.location.origin;
        const basePath = window.location.pathname.replace(/\/[^\/]*$/, '');
        return `${origin}${basePath}/api/index.php`;
    })(),
    MAP_API_URL: (function() {
        const origin = window.location.origin;
        const basePath = window.location.pathname.replace(/\/[^\/]*$/, '');
        return `${origin}${basePath}/api/index.php?mapa`;
    })()
};

// Utilitários
const Utils = {
    showLoading: function(element) {
        element.innerHTML = '<div class="loading"></div>';
    },
    
    showNotification: function(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()">&times;</button>
        `;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? 'rgba(0, 255, 136, 0.1)' : 'rgba(255, 107, 107, 0.1)'};
            border: 1px solid ${type === 'success' ? 'rgba(0, 255, 136, 0.3)' : 'rgba(255, 107, 107, 0.3)'};
            color: ${type === 'success' ? '#00ff88' : '#ff6b6b'};
            padding: 15px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 10000;
            backdrop-filter: blur(10px);
            animation: fadeIn 0.3s ease-out;
        `;
        
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 5000);
    },
    
    formatNumber: function(num) {
        return new Intl.NumberFormat('pt-BR').format(num);
    },
    
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};

// API Service
const ApiService = {
    async request(endpoint, options = {}) {
        const url = `${CONFIG.API_BASE_URL}/${endpoint}`;
        
        try {
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                ...options
            });
            
            const data = await response.json();
            
            if (!data.sucesso) {
                throw new Error(data.mensagem || 'Erro na requisição');
            }
            
            return data;
        } catch (error) {
            console.error(`API Error (${endpoint}):`, error);
            throw error;
        }
    },
    
    // Países
    getCountries: function() {
        return this.request('paises/listar');
    },
    
    getCountry: function(id) {
        return this.request(`paises/obter/${id}`);
    },
    
    createCountry: function(data) {
        return this.request('paises/criar', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },
    
    updateCountry: function(id, data) {
        return this.request(`paises/atualizar/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },
    
    deleteCountry: function(id) {
        return this.request(`paises/deletar/${id}`, {
            method: 'DELETE'
        });
    },
    
    searchCountries: function(term) {
        return this.request(`paises/buscar?q=${encodeURIComponent(term)}`);
    },
    
    // Cidades
    getCities: function() {
        return this.request('cidades/listar');
    },
    
    getCity: function(id) {
        return this.request(`cidades/obter/${id}`);
    },
    
    createCity: function(data) {
        return this.request('cidades/criar', {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },
    
    updateCity: function(id, data) {
        return this.request(`cidades/atualizar/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },
    
    deleteCity: function(id) {
        return this.request(`cidades/deletar/${id}`, {
            method: 'DELETE'
        });
    },
    
    searchCities: function(term) {
        return this.request(`cidades/buscar?q=${encodeURIComponent(term)}`);
    },
    
    // Mapa
    getMapData: function() {
        return fetch(CONFIG.MAP_API_URL)
            .then(response => response.json())
            .then(data => {
                if (!data.sucesso) {
                    throw new Error(data.mensagem || 'Erro ao carregar mapa');
                }
                return data;
            });
    },
    
    // Clima
    getWeather: function(cityId) {
        return this.request(`cidades/clima/${cityId}`);
    },
    
    // API Externa
    getCountryInfoFromAPI: function(code) {
        return this.request(`paises/informacoes-api?codigo=${code}`);
    },
    
    importFromAPI: function(code) {
        return this.request(`paises/importar-api?codigo=${code}`);
    }
};

// Sistema principal
class CRUDMundo {
    constructor() {
        this.countries = [];
        this.cities = [];
        this.init();
    }
    
    async init() {
        try {
            await this.loadData();
            this.setupEventListeners();
            Utils.showNotification('Sistema carregado com sucesso!', 'success');
        } catch (error) {
            console.error('Erro na inicialização:', error);
            Utils.showNotification('Erro ao carregar dados do sistema', 'error');
        }
    }
    
    async loadData() {
        try {
            // Carregar países
            const countriesData = await ApiService.getCountries();
            this.countries = countriesData.dados || [];
            this.renderCountries(this.countries);
            
            // Carregar cidades
            const citiesData = await ApiService.getCities();
            this.cities = citiesData.dados || [];
            this.renderCities(this.cities);
            
            // Carregar estatísticas
            await this.loadStatistics();
            
            // Popular select de países
            this.populateCountrySelect();
            
        } catch (error) {
            console.error('Erro ao carregar dados:', error);
            throw error;
        }
    }
    
    async loadStatistics() {
        try {
            const statsData = await ApiService.request('paises/estatisticas');
            if (statsData.sucesso) {
                this.updateStats(statsData.dados);
            }
        } catch (error) {
            console.error('Erro ao carregar estatísticas:', error);
        }
    }
    
    updateStats(stats) {
        // Implementação das estatísticas
        document.getElementById('totalPaises').textContent = Utils.formatNumber(stats.total_paises || 0);
        document.getElementById('totalContinentes').textContent = stats.total_continentes || 0;
        
        // Atualizar população total
        let totalPopulation = this.countries.reduce((sum, country) => {
            return sum + (parseInt(country.populacao) || 0);
        }, 0);
        document.getElementById('populacaoTotal').textContent = Utils.formatNumber(totalPopulation);
    }
    
    renderCountries(countries) {
        const tbody = document.querySelector('#tabelaPaises tbody');
        if (!tbody) return;
        
        tbody.innerHTML = countries.map(country => `
            <tr class="fade-in">
                <td>${country.id_pais}</td>
                <td>
                    ${country.bandeira_url ? 
                        `<img src="${country.bandeira_url}" alt="${country.nome}" class="entity-flag">` : 
                        `<i class="fas fa-flag" style="color: var(--accent-color);"></i>`
                    }
                </td>
                <td>
                    <strong>${country.nome}</strong>
                    ${country.codigo_iso ? `<span class="badge badge-info" style="margin-left: 8px;">${country.codigo_iso}</span>` : ''}
                </td>
                <td>${country.continente}</td>
                <td>${Utils.formatNumber(country.populacao)}</td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <button class="btn-outline" onclick="crudMundo.editCountry(${country.id_pais})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-outline" onclick="crudMundo.deleteCountry(${country.id_pais})" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                        ${country.codigo_iso ? `
                        <button class="btn-outline" onclick="crudMundo.showCountryInfo(${country.id_pais})" title="Informações da API">
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `).join('');
    }
    
    renderCities(cities) {
        const tbody = document.querySelector('#tabelaCidades tbody');
        if (!tbody) return;
        
        tbody.innerHTML = cities.map(city => `
            <tr class="fade-in">
                <td>${city.id_cidade}</td>
                <td><strong>${city.nome}</strong></td>
                <td>${Utils.formatNumber(city.populacao)}</td>
                <td>${city.pais_nome || 'N/A'}</td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <button class="btn-outline" onclick="crudMundo.editCity(${city.id_cidade})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-outline" onclick="crudMundo.deleteCity(${city.id_cidade})" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button class="btn-outline" onclick="crudMundo.showWeather(${city.id_cidade})" title="Clima">
                            <i class="fas fa-cloud-sun"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
    
    populateCountrySelect() {
        const select = document.getElementById('cidadePais');
        if (!select) return;
        
        select.innerHTML = '<option value="">Selecione um país...</option>' +
            this.countries.map(country => 
                `<option value="${country.id_pais}">${country.nome}</option>`
            ).join('');
    }
    
    // Métodos para países
    async editCountry(id) {
        try {
            const countryData = await ApiService.getCountry(id);
            if (countryData.sucesso) {
                this.openCountryModal(countryData.dados);
            }
        } catch (error) {
            Utils.showNotification('Erro ao carregar país', 'error');
        }
    }
    
    async deleteCountry(id) {
        if (!confirm('Tem certeza que deseja excluir este país? Esta ação não pode ser desfeita.')) {
            return;
        }
        
        try {
            const result = await ApiService.deleteCountry(id);
            if (result.sucesso) {
                Utils.showNotification('País excluído com sucesso!', 'success');
                await this.loadData();
            }
        } catch (error) {
            Utils.showNotification(error.message || 'Erro ao excluir país', 'error');
        }
    }
    
    async showCountryInfo(countryId) {
        try {
            const country = this.countries.find(c => c.id_pais == countryId);
            if (!country || !country.codigo_iso) {
                Utils.showNotification('Este país não tem código ISO cadastrado', 'error');
                return;
            }
            
            const info = await ApiService.getCountryInfoFromAPI(country.codigo_iso);
            if (info.sucesso) {
                this.showCountryInfoModal(info.dados);
            }
        } catch (error) {
            Utils.showNotification('Erro ao obter informações da API', 'error');
        }
    }
    
    // Métodos para cidades
    async editCity(id) {
        try {
            const cityData = await ApiService.getCity(id);
            if (cityData.sucesso) {
                this.openCityModal(cityData.dados);
            }
        } catch (error) {
            Utils.showNotification('Erro ao carregar cidade', 'error');
        }
    }
    
    async deleteCity(id) {
        if (!confirm('Tem certeza que deseja excluir esta cidade? Esta ação não pode ser desfeita.')) {
            return;
        }
        
        try {
            const result = await ApiService.deleteCity(id);
            if (result.sucesso) {
                Utils.showNotification('Cidade excluída com sucesso!', 'success');
                await this.loadData();
            }
        } catch (error) {
            Utils.showNotification(error.message || 'Erro ao excluir cidade', 'error');
        }
    }
    
    async showWeather(cityId) {
        try {
            const weatherData = await ApiService.getWeather(cityId);
            if (weatherData.sucesso) {
                this.showWeatherModal(weatherData.dados);
            }
        } catch (error) {
            Utils.showNotification('Erro ao obter dados do clima', 'error');
        }
    }
    
    // Modais
    openCountryModal(country = null) {
        const modal = document.getElementById('modalPais');
        const title = document.getElementById('modalPaisTitulo');
        const form = document.getElementById('formPais');
        
        if (country) {
            title.textContent = 'Editar País';
            document.getElementById('paisId').value = country.id_pais;
            document.getElementById('paisNome').value = country.nome;
            document.getElementById('paisContinente').value = country.continente;
            document.getElementById('paisPopulacao').value = country.populacao;
            document.getElementById('paisIdioma').value = country.idioma;
            document.getElementById('paisCodigoISO').value = country.codigo_iso || '';
        } else {
            title.textContent = 'Novo País';
            form.reset();
            document.getElementById('paisId').value = '';
        }
        
        modal.style.display = 'flex';
    }
    
    openCityModal(city = null) {
        const modal = document.getElementById('modalCidade');
        const title = document.getElementById('modalCidadeTitulo');
        const form = document.getElementById('formCidade');
        
        this.populateCountrySelect();
        
        if (city) {
            title.textContent = 'Editar Cidade';
            document.getElementById('cidadeId').value = city.id_cidade;
            document.getElementById('cidadeNome').value = city.nome;
            document.getElementById('cidadePopulacao').value = city.populacao;
            document.getElementById('cidadePais').value = city.id_pais;
        } else {
            title.textContent = 'Nova Cidade';
            form.reset();
            document.getElementById('cidadeId').value = '';
        }
        
        modal.style.display = 'flex';
    }
    
    showCountryInfoModal(info) {
        // Implementação do modal de informações da API
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <button class="modal-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                <h2 style="color: var(--accent-color); margin-bottom: 20px;">
                    <i class="fas fa-globe-americas"></i> ${info.nome_oficial}
                </h2>
                <div style="text-align: center; margin: 20px 0;">
                    <img src="${info.bandeira}" alt="Bandeira" style="width: 200px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.3);">
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0;">
                    <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; border: 1px solid rgba(0,255,136,0.2);">
                        <div style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 5px;">🌎 Capital</div>
                        <div style="font-weight: bold; font-size: 1.1rem;">${info.capital}</div>
                    </div>
                    <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; border: 1px solid rgba(0,255,136,0.2);">
                        <div style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 5px;">👥 População</div>
                        <div style="font-weight: bold; font-size: 1.1rem;">${Utils.formatNumber(info.populacao)}</div>
                    </div>
                    <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; border: 1px solid rgba(0,255,136,0.2);">
                        <div style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 5px;">📏 Área</div>
                        <div style="font-weight: bold; font-size: 1.1rem;">${Utils.formatNumber(info.area)} km²</div>
                    </div>
                    <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; border: 1px solid rgba(0,255,136,0.2);">
                        <div style="color: var(--accent-color); font-size: 0.9rem; margin-bottom: 5px;">🗣️ Idiomas</div>
                        <div style="font-weight: bold; font-size: 1.1rem;">${info.idiomas.join(', ')}</div>
                    </div>
                </div>
                ${info.mapa ? `
                <div style="text-align: center; margin-top: 20px;">
                    <a href="${info.mapa}" target="_blank" class="btn">
                        <i class="fas fa-map-marked-alt"></i> Ver no Google Maps
                    </a>
                </div>
                ` : ''}
            </div>
        `;
        document.body.appendChild(modal);
        modal.style.display = 'flex';
    }
    
    showWeatherModal(weather) {
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <button class="modal-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                <h2 style="color: var(--accent-color); margin-bottom: 20px;">
                    <i class="fas fa-cloud-sun"></i> Clima em ${weather.cidade}
                </h2>
                <div style="text-align: center; margin: 30px 0;">
                    <div style="font-size: 4rem; font-weight: bold; color: var(--accent-color); margin-bottom: 10px;">
                        ${weather.temperatura}°C
                    </div>
                    <div style="color: var(--text-secondary); font-size: 1.2rem;">${weather.condicao}</div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 30px 0;">
                    <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; text-align: center;">
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">Sensação</div>
                        <div style="font-weight: bold; font-size: 1.2rem; color: var(--accent-color);">${weather.sensacao_termica}°C</div>
                    </div>
                    <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; text-align: center;">
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">Mínima</div>
                        <div style="font-weight: bold; font-size: 1.2rem; color: var(--accent-color);">${weather.minima}°C</div>
                    </div>
                    <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; text-align: center;">
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">Máxima</div>
                        <div style="font-weight: bold; font-size: 1.2rem; color: var(--accent-color);">${weather.maxima}°C</div>
                    </div>
                    <div style="background: rgba(0,255,136,0.05); padding: 15px; border-radius: 10px; text-align: center;">
                        <div style="color: var(--text-secondary); font-size: 0.9rem;">Umidade</div>
                        <div style="font-weight: bold; font-size: 1.2rem; color: var(--accent-color);">${weather.umidade}%</div>
                    </div>
                </div>
                <div style="color: var(--text-tertiary); text-align: center; font-size: 0.9rem; margin-top: 20px;">
                    Fonte: ${weather.fonte} | Atualizado: ${weather.atualizado}
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.style.display = 'flex';
    }
    
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    }
    
    setupEventListeners() {
        // Formulário de país
        document.getElementById('formPais')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleCountrySubmit();
        });
        
        // Formulário de cidade
        document.getElementById('formCidade')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleCitySubmit();
        });
        
        // Busca de países
        const searchCountryInput = document.getElementById('buscaPais');
        if (searchCountryInput) {
            searchCountryInput.addEventListener('input', Utils.debounce(async (e) => {
                await this.searchCountries(e.target.value);
            }, 300));
        }
        
        // Busca de cidades
        const searchCityInput = document.getElementById('buscaCidade');
        if (searchCityInput) {
            searchCityInput.addEventListener('input', Utils.debounce(async (e) => {
                await this.searchCities(e.target.value);
            }, 300));
        }
    }
    
    async handleCountrySubmit() {
        const form = document.getElementById('formPais');
        const id = document.getElementById('paisId').value;
        const formData = {
            nome: document.getElementById('paisNome').value,
            continente: document.getElementById('paisContinente').value,
            populacao: document.getElementById('paisPopulacao').value,
            idioma: document.getElementById('paisIdioma').value,
            codigo_iso: document.getElementById('paisCodigoISO').value
        };
        
        try {
            let result;
            if (id) {
                result = await ApiService.updateCountry(id, formData);
            } else {
                result = await ApiService.createCountry(formData);
            }
            
            if (result.sucesso) {
                Utils.showNotification(result.mensagem, 'success');
                this.closeModal('pais');
                await this.loadData();
            }
        } catch (error) {
            Utils.showNotification(error.message || 'Erro ao salvar país', 'error');
        }
    }
    
    async handleCitySubmit() {
        const form = document.getElementById('formCidade');
        const id = document.getElementById('cidadeId').value;
        const formData = {
            nome: document.getElementById('cidadeNome').value,
            populacao: document.getElementById('cidadePopulacao').value,
            id_pais: document.getElementById('cidadePais').value
        };
        
        try {
            let result;
            if (id) {
                result = await ApiService.updateCity(id, formData);
            } else {
                result = await ApiService.createCity(formData);
            }
            
            if (result.sucesso) {
                Utils.showNotification(result.mensagem, 'success');
                this.closeModal('cidade');
                await this.loadData();
            }
        } catch (error) {
            Utils.showNotification(error.message || 'Erro ao salvar cidade', 'error');
        }
    }
    
    async searchCountries(term) {
        if (!term) {
            this.renderCountries(this.countries);
            return;
        }
        
        try {
            const result = await ApiService.searchCountries(term);
            if (result.sucesso) {
                this.renderCountries(result.dados);
            }
        } catch (error) {
            console.error('Erro na busca:', error);
        }
    }
    
    async searchCities(term) {
        if (!term) {
            this.renderCities(this.cities);
            return;
        }
        
        try {
            const result = await ApiService.searchCities(term);
            if (result.sucesso) {
                this.renderCities(result.dados);
            }
        } catch (error) {
            console.error('Erro na busca:', error);
        }
    }
    
    async importFromAPI() {
        const code = prompt('Digite o código do país (ex: BRA, USA, FRA, DEU, JPN):');
        if (!code) return;
        
        try {
            const result = await ApiService.importFromAPI(code.toUpperCase());
            if (result.sucesso) {
                Utils.showNotification(`✅ ${result.mensagem}`, 'success');
                await this.loadData();
            }
        } catch (error) {
            Utils.showNotification(`❌ ${error.message}`, 'error');
        }
    }
    
    exportData() {
        const data = {
            countries: this.countries,
            cities: this.cities,
            exportDate: new Date().toISOString()
        };
        
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `crud-mundo-backup-${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        Utils.showNotification('Dados exportados com sucesso!', 'success');
    }
}

// Inicializar sistema
let crudMundo;

document.addEventListener('DOMContentLoaded', () => {
    crudMundo = new CRUDMundo();
    
    // Funções globais para uso nos botões
    window.crudMundo = crudMundo;
    
    window.abrirModal = (tipo) => {
        if (tipo === 'pais') {
            crudMundo.openCountryModal();
        } else if (tipo === 'cidade') {
            crudMundo.openCityModal();
        }
    };
    
    window.fecharModal = (tipo) => {
        const modalId = `modal${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`;
        crudMundo.closeModal(modalId);
    };
    
    window.importarPaisAPI = () => crudMundo.importFromAPI();
    window.exportarDados = () => crudMundo.exportData();
    window.atualizarEstatisticas = () => crudMundo.loadStatistics();
    
    // Mapa interativo
    const mapButton = document.querySelector('a[href="mapa.html"]');
    if (mapButton) {
        mapButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.open('mapa.html', '_blank');
        });
    }
});