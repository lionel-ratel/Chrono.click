if("undefined"===typeof JCck)var JCck={};
if("undefined"===typeof JCck.Uikit){JCck.Uikit={}};

(function($) {
	JCck.Uikit.modal = {
		current: null,
		config: {
			spinner: '<div class="uk-flex uk-flex-center uk-flex-middle uk-height-small"><div uk-spinner="ratio: 1.5"></div></div>'
		},
		groups: {
			ajax: []
		},
		next: null,
		url_delete: null,

		close: function() {
			this.hide();
		},

		delete: function(el) {
			const row = el.closest('tr');			
			const pk = row.getAttribute('data-pk');

			if (!pk) {
				UIkit.notification({ message: 'ID de suppression non définie', status: 'danger' });
				return;
			}

			UIkit.modal.confirm('Voulez-vous vraiment supprimer cet élément ?', {
				labels: { ok: 'Supprimer', cancel: 'Annuler' },
				stack: true
			}).then(async () => {
				const loading = UIkit.modal.dialog(`<div class="uk-modal-body">${this.config.spinner}</div>`);

				try {
					const response = await fetch(this.url_delete, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: new URLSearchParams({
							'pk': pk
						})
					});

					if (!response.ok) throw new Error('Erreur lors de la suppression');

					const result = await response.json();

					loading.hide();
					
					if (result.success) {
						UIkit.notification({ message: 'Suppression réussie', status: 'success' });
						row.remove();
					} else {
						throw new Error(result.message || 'Échec de la suppression');
					}

				} catch (error) {
					loading.hide();
					console.error(error);
					UIkit.notification({ message: error.message, status: 'danger' });
				}
			}, () => {
				console.log('Suppression annulée');
			});
		},

		hide: function() {
			if (this.current) {
				this.current.hide();
				this.current = null;
			}
		},

		init: function() {
		},

		initFilter: function() {
			const input = document.getElementById('o_builder_keywords');
			if (!input) return;

			const wrapper = input.parentElement;
			wrapper.classList.add('uk-inline', 'uk-width-1-1');
			input.style.paddingRight = "40px";

			const resetBtn = document.createElement('a');
			resetBtn.setAttribute('uk-icon', 'icon: plus');
			resetBtn.className = 'uk-icon-button uk-text-danger';
			resetBtn.style.cssText = `
				width: 26px; height: 26px; position: absolute; right: 8px; top: 50%; 
				transform: translateY(-50%) rotate(45deg); display: none; z-index: 10; line-height: 26px;
			`;

			wrapper.appendChild(resetBtn);
			const items = document.querySelectorAll('#modal-content-body tr[data-title]');

			input.addEventListener('input', () => {
				const val = input.value.toLowerCase();
				resetBtn.style.display = val.length > 0 ? 'inline-flex' : 'none';
				items.forEach(tr => {
					const title = tr.getAttribute('data-title').toLowerCase();
					tr.style.display = title.includes(val) ? '' : 'none';
				});
			});

			resetBtn.addEventListener('click', (e) => {
				e.preventDefault();
				input.value = '';
				resetBtn.style.display = 'none';
				items.forEach(tr => tr.style.display = '');
				input.focus();
			});
		},

		initInstances: function() {
			if (!JCck.More || !JCck.More.ItemX || !JCck.More.ItemX.instances) return;
			
			const instances = JCck.More.ItemX.instances;

			Object.keys(instances).forEach(key => {
				const instance = instances[key];
				const urlProps = ['link_add', 'link_list', 'link_process', 'link_save', 'link_select'];

				urlProps.forEach(prop => {
					if (instance[prop] && typeof instance[prop] === 'string') {
						if (!instance[prop].includes('form/')) {
							instance[prop] = instance[prop].replace(/\/(administrator|admin)\//, "/");
						}
					}
				});
			});
		},

		insert: async function(result) {
			const params = new URLSearchParams({
				format: 'raw',
				infinite: 1,
				pks: result.pks
			});

			let base_url = JCck.More.ItemX.instances[JCck.More.ItemX.active].link_list;

			const active_instance = JCck.More.ItemX.instances[JCck.More.ItemX.active];
			const referrer_string = `"referrer":"${active_instance.referrer}.${JCck.More.ItemX.active}"`;

			let final_url = base_url.replace('"referrer":""', referrer_string);
			final_url += (final_url.includes('?') ? '&' : '?') + params.toString();

			try {
		        const response = await fetch(final_url, {
		            method: 'GET',
		            cache: 'no-cache'
		        });

		        if (!response.ok) {
		            throw new Error(`Erreur HTTP: ${response.status}`);
		        }

		        const html = await response.text();
				const template = document.createElement('template');
				template.innerHTML = html.trim();
				const new_tr = template.content.firstChild;

				//
				const container = document.getElementById(JCck.More.ItemX.active);
				const tr = container.querySelector(`tr[data-pk="${result.pk}"]`);

				if (result.order > 0) {
					tr.after(new_tr);
				} else {
					tr.before(new_tr);
				}
		    } catch (error) {
		        console.error("Erreur lors de l'appel fetch :", error);
		    }
		},

		loadUrl: async function(url) {
			const isForm = url.includes('form/');
			const cleanUrl = !isForm ? url.replace(/\/(administrator|admin)\//, "/") : url;

			try {
				const response = await fetch(cleanUrl);
				if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
				const htmlContent = await response.text();
				
				const modalTitle = isForm ? "Ajouter une section" : "Sélectionner une section";
				const modalHtml = `
					<div class="uk-flex uk-flex-column" style="height: 85vh;">
						<div class="uk-modal-header">
							<button class="uk-modal-close-default" type="button" uk-close></button>
							<h2 class="uk-modal-title uk-text-lead">${modalTitle}</h2>
						</div>
						<div id="modal-content-body" class="uk-modal-body uk-overflow-auto uk-flex-1">
							${htmlContent}
						</div>
					</div>
				`;

				this.current = UIkit.modal.dialog(modalHtml);

				UIkit.svg('.uk-modal-body [uk-svg]');
				UIkit.grid('.uk-modal-body [uk-grid]');

				if (isForm) {
					const cancelBtn = document.querySelector('#modal-content-body .button-cancel, #modal-content-body [data-task="cancel"]');
					if (cancelBtn) {
						cancelBtn.addEventListener('click', (e) => {
							e.preventDefault();
							this.close();
						});
					}
				} else {
					this.initFilter();
				}

			} catch (error) {
				loadingModal.hide();
				UIkit.notification({ message: 'Erreur de chargement', status: 'danger' });
			}
		},

		openPicker: function(el) {
			let order = 1, pk = 0;

			if (el.tagName === 'A') {
				const tr = el.closest('tr');
				order = el.getAttribute('data-order');
				pk = tr.getAttribute('data-pk');
			}

			this.next = {order: order, pk: pk};
			JCck.More.ItemX.setFromClick(el).select();
		}, 

		select: async function(el) {
			const row = el.closest('tr');
	
			if (row) {
				const dataType = row.getAttribute('data-type');
				try {
					const response = await fetch(this.url_insert, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: new URLSearchParams({
							'pk': this.next.pk,
							'order': this.next.order,
							'type': dataType
						})
					});

					if (!response.ok) throw new Error('Erreur lors de la suppression');

					const result = await response.json();
					
					if (result.success) {
						this.insert(result);
						this.hide();
						UIkit.notification({ message: 'Section ajouté', status: 'success' });
					} else {
						throw new Error(result.message || 'Échec de l\'ajout');
					}

				} catch (error) {
					loading.hide();
					UIkit.notification({ message: error.message, status: 'danger' });
				}
			}
		}
	};
})(jQuery);