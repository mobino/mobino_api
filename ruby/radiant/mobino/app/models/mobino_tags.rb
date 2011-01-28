module MobinoTags 
  
  include Radiant::Taggable
  
  class TagError < StandardError; end
    
  tag "mobino" do |tag|
    amount = tag.attr['amount']
    reference_number = tag.attr['reference_number']
    merchant_id = tag.attr['merchant_id']
    env = tag.attr['env']  || 'production'
    transaction_type = tag.attr['transaction_type']  || 'regular'
    api_key = settings(env.to_sym)[:api_key]
    api_secret = settings(env.to_sym)[:api_secret]
    data_hash = parametrize(merchant_id, amount, reference_number, api_key, transaction_type)
    
    signature = Signum::Signature.for(api_secret, data_hash)
    "{'amount':'#{amount}', 'merchant_id': #{merchant_id}, 'api_key':'#{api_key}', 'reference_number': '#{reference_number}', 'signature': '#{signature.signature}', 'transaction_type': '#{transaction_type}'}"
  end
    
  protected 
    def parametrize merchant_id, amount, reference_number, api_key, transaction_type
      {
        'merchant_id'       => merchant_id,
        'amount'            => amount,
        'reference_number'  => reference_number,
        'api_key'           => api_key,
        'transaction_type'  => transaction_type
      }
    end
    
    def settings(key)
      $settings ||= YAML.load_file(File.join(RAILS_ROOT, 'vendor', 'extensions', 'widget', 'config', 'settings.yml'))[RAILS_ENV.to_sym]

      unless $settings.include?(key)
        message = "No setting defined for #{key.inspect}."
        defined?(logger) ? logger.warn(message) : $stderr.puts(message)
      end

      $settings[key]
    end
end

